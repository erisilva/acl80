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

        $roles = new Role;

        // filtros
        if (request()->has('name')){
            $roles = $roles->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('description')){
            $roles = $roles->where('description', 'like', '%' . request('description') . '%');
        }            
        // ordena
        $roles = $roles->orderBy('name', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $roles = $roles->paginate(session('perPage', '5'))->appends([          
            'name' => request('name'),
            'description' => request('description'),           
            ]);

        return view('admin.roles.index', compact('roles', 'perpages'));
    }

    public function create()
    {
        if (Gate::denies('role-create')) {
            abort(403, 'Acesso negado.');
        }

        // listagem de perfis (roles)
        $permissions = Permission::orderBy('name','asc')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
          'name' => 'required',
          'description' => 'required',
        ]);

        $role = $request->all();

        $newRole = Role::create($role); //salva

        // salva os perfis (roles)
        if(isset($role['permissions']) && count($role['permissions'])){
            foreach ($role['permissions'] as $key => $value) {
                $newRole->permissions()->attach($value);
            }

        } 

        Session::flash('create_role', 'Perfil cadastrado com sucesso!');

        return redirect(route('roles.index'));
    }

    public function show($id)
    {
        if (Gate::denies('role-show')) {
            abort(403, 'Acesso negado.');
        }

        // perfil que será exibido e pode ser excluido
        $role = Role::findOrFail($id);

        return view('admin.roles.show', compact('role'));
    }

    public function edit($id)
    {
        if (Gate::denies('role-edit')) {
            abort(403, 'Acesso negado.');
        }

        // perfil que será alterado
        $role = Role::findOrFail($id);

        // listagem de perfis (roles)
        $permissions = Permission::orderBy('name','asc')->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
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
