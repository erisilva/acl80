<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role; // Perfil
use App\Models\Permission; // Permissões
use App\Models\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

use App\Exports\RolesExport;
use Maatwebsite\Excel\Facades\Excel;

use Barryvdh\DomPDF\Facade\Pdf;

class RoleController extends Controller
{

    public function __construct() 
    {
        $this->middleware(['middleware' => 'auth']);
        $this->middleware(['middleware' => 'hasaccess']);
    }

    public function index()
    {
        if (Gate::denies('role-index')) {
            abort(403, 'Acesso negado.');
        }

        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        return view('admin.roles.index', [
            'roles' => Role::orderBy('id', 'asc')->filter(request(['name', 'description']))->paginate(session('perPage', '5'))->appends(request(['name', 'description'])),
            'perpages' => Perpage::orderBy('valor')->get()
        ]);
    }

    public function create()
    {
        if (Gate::denies('role-create')) {
            abort(403, 'Acesso negado.');
        }

        return view('admin.roles.create', [
            'permissions' => Permission::orderBy('name','asc')->get()
        ]);
    }

    public function store(Request $request)
    {
        // Nota: o $request->validate retorna só os campos que forem validados na lista
        $this->validate($request, [
          'name' => 'required',
          'description' => 'required',
        ]);        

        DB::beginTransaction();

        try{
            $role = $request->all();

            $newRole = Role::create($role);

            // salva os perfis (roles)
            if(isset($role['permissions']) && count($role['permissions'])){
                foreach ($role['permissions'] as $key => $value) {
                    $newRole->permissions()->attach($value);
                }
            }

            DB::commit();

            return redirect(route('roles.index'))->with('message', 'Perfil cadastrado com sucesso!');

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('roles.index')->with('message','Erro ao incluir');
        }
    }

    public function show($id)
    {
        if (Gate::denies('role-show')) {
            abort(403, 'Acesso negado.');
        }

        return view('admin.roles.show', [
            'role' => Role::findOrFail($id)
        ]);
    }

    public function edit($id)
    {
        if (Gate::denies('role-edit')) {
            abort(403, 'Acesso negado.');
        }

        return view('admin.roles.edit', [
            'role' => Role::findOrFail($id),
            'permissions' => Permission::orderBy('name','asc')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
          'name' => 'required',
          'description' => 'required',
        ]);

        $role = Role::findOrFail($id);

        // recebe todos valores entrados no formulário
        $input = $request->all();

        // remove todos as permissões vinculadas a esse operador
        $permissions = $role->permissions;
        if(count($permissions)){
            foreach ($permissions as $key => $value) {
               $role->permissions()->detach($value->id);
            }
        }

        // vincula os novas permissões desse operador
        if(isset($input['permissions']) && count($input['permissions'])){
            foreach ($input['permissions'] as $key => $value) {
               $role->permissions()->attach($value);
            }
        }
            
        $role->update($input);
        
        Session::flash('edited_role', 'Perfil alterado com sucesso!');

        return redirect(route('roles.edit', $id));
    }

    public function destroy($id)
    {
        if (Gate::denies('role-delete')) {
            abort(403, 'Acesso negado.');
        }

        Role::findOrFail($id)->delete();

        Session::flash('deleted_role', 'Permissão excluída com sucesso!');

        return redirect(route('roles.index'));
    }

    public function exportcsv()
    {
        if (Gate::denies('role-export')) {
            abort(403, 'Acesso negado.');
        }

        # filtragem
        $filter_name = (request()->has('name') ? request('name') : '');
        
        $filter_description = (request()->has('description') ? request('description') : '');

        return Excel::download(new RolesExport($filter_name, $filter_description), 'Perfis_' .  date("Y-m-d H:i:s") . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportxls()
    {
        if (Gate::denies('role-export')) {
            abort(403, 'Acesso negado.');
        }

        # filtragem
        $filter_name = (request()->has('name') ? request('name') : '');
        
        $filter_description = (request()->has('description') ? request('description') : '');

        return Excel::download(new RolesExport($filter_name, $filter_description), 'Perfis_' .  date("Y-m-d H:i:s") . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportpdf()
    {
        if (Gate::denies('role-export')) {
            abort(403, 'Acesso negado.');
        }

        # tratamento dos filtros
        $filter_name = (request()->has('name') ? request('name') : '');
        
        $filter_description = (request()->has('description') ? request('description') : '');

        # criação do dataset
        $dataset = new Role;

        $dataset = $dataset->select('name', 'description');

        if (!empty($filter_name)){
            $dataset = $dataset->where('name', 'like', '%' . $filter_name . '%');    
        }

        if (!empty($filter_description)){
            $dataset = $dataset->Where('description', 'like', '%' . $filter_description . '%');
        }

        $dataset = $dataset->get();

        $pdf = PDF::loadView('admin.roles.report', compact('dataset'));
        
        return $pdf->download('Perfis_' .  date("Y-m-d H:i:s") . '.pdf');
    }     
}
