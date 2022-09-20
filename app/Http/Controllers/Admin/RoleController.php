<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role; // Perfil
use App\Models\Permission; // Permissões
use App\Models\Perpage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $this->authorize('role-index');
        
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
        $this->authorize('role-create');
        
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
        $this->authorize('role-show');

        return view('admin.roles.show', [
            'role' => Role::findOrFail($id)
        ]);
    }

    public function edit($id)
    {
        $this->authorize('role-edit');

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

        DB::beginTransaction();

        try{

            $role = Role::findOrFail($id);

            // recebe todos valores entrados no formulário
            $input = $request->all();

            // remove todos as permissões vinculadas a esse operador
            $role->permissions()->detach();

            // vincula os novas permissões desse operador
            if(isset($input['permissions']) && count($input['permissions'])){
                foreach ($input['permissions'] as $key => $value) {
                   $role->permissions()->attach($value);
                }
            }
                
            $role->update($input);

            DB::commit();

            return redirect(route('roles.edit', $id))->with('message', 'Perfil alterado com sucesso!');

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('roles.index')->with('message','Erro ao alterar perfil');
        }
    }

    public function destroy($id)
    {
        $this->authorize('role-delete');

        Role::findOrFail($id)->delete();

        return redirect(route('roles.index'))->with('message', 'Perfil excluído com sucesso!');
    }

    public function exportcsv()
    {
        $this->authorize('role-export');

        return Excel::download(new RolesExport(request(['name','description'])), 'Perfis_' .  date("Y-m-d H:i:s") . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportxls()
    {
        $this->authorize('role-export');

        return Excel::download(new RolesExport(request(['name','description'])), 'Perfis_' .  date("Y-m-d H:i:s") . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportpdf()
    {
        $this->authorize('role-export');

        return PDF::loadView('admin.roles.report', [
            'dataset' => Role::orderBy('id', 'asc')->filter(request(['name', 'description']))->get()
        ])->download('Perfis_' .  date("Y-m-d H:i:s") . '.pdf');
    }     
}
