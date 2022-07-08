<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

use App\Exports\PermissionsExport;
use Maatwebsite\Excel\Facades\Excel;

use Barryvdh\DomPDF\Facade\Pdf;

class PermissionController extends Controller
{  
    public function __construct() 
    {
        $this->middleware(['middleware' => 'auth']);
        $this->middleware(['middleware' => 'hasaccess']);
    }

    public function index()
    {
        if (Gate::denies('permission-index')) {
            abort(403, 'Acesso negado.');
        }

        $permissions = new Permission;

        // filtros
        if (request()->has('name')){
            $permissions = $permissions->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('description')){
            $permissions = $permissions->where('description', 'like', '%' . request('description') . '%');
        }

        // ordena
        $permissions = $permissions->orderBy('name', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $permissions = $permissions->paginate(session('perPage', '5'))->appends([          
            'name' => request('name'),
            'description' => request('description'),           
            ]);

        return view('admin.permissions.index', compact('permissions', 'perpages'));
    }

    public function create()
    {
        if (Gate::denies('permission-create')) {
            abort(403, 'Acesso negado.');
        }

        return view('admin.permissions.create');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
          'name' => 'required',
          'description' => 'required',
        ]);

        $permission = $request->all();

        Permission::create($permission); //salva

        Session::flash('create_permission', 'Permissão cadastrada com sucesso!');

        return redirect(route('permissions.index'));
    }


    public function show($id)
    {
        if (Gate::denies('permission-show')) {
            abort(403, 'Acesso negado.');
        }

        // permissão que será exibido e pode ser excluido
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.show', compact('permission'));
    }

    public function edit($id)
    {
        if (Gate::denies('permission-edit')) {
            abort(403, 'Acesso negado.');
        }

        // usuário que será alterado
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
          'name' => 'required',
          'description' => 'required',
        ]);

        $permission = Permission::findOrFail($id);
            
        $permission->update($request->all());
        
        Session::flash('edited_permission', 'Permissão alterada com sucesso!');

        return redirect(route('permissions.edit', $id));
    }

    public function destroy($id)
    {
        if (Gate::denies('permission-delete')) {
            abort(403, 'Acesso negado.');
        }

        Permission::findOrFail($id)->delete();

        Session::flash('deleted_permission', 'Permissão excluída com sucesso!');

        return redirect(route('permissions.index'));
    }

    public function exportcsv()
    {
        if (Gate::denies('permission-export')) {
            abort(403, 'Acesso negado.');
        }

        # filtragem
        $filter_name = (request()->has('name') ? request('name') : '');
        
        $filter_description = (request()->has('description') ? request('description') : '');

        return Excel::download(new PermissionsExport($filter_name, $filter_description), 'Permissoes_' .  date("Y-m-d H:i:s") . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportxls()
    {
        if (Gate::denies('permission-export')) {
            abort(403, 'Acesso negado.');
        }

        # filtragem
        $filter_name = (request()->has('name') ? request('name') : '');
        
        $filter_description = (request()->has('description') ? request('description') : '');

        return Excel::download(new PermissionsExport($filter_name, $filter_description), 'Permissoes_' .  date("Y-m-d H:i:s") . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportpdf()
    {
        if (Gate::denies('permission-export')) {
            abort(403, 'Acesso negado.');
        }

        # tratamento dos filtros
        $filter_name = (request()->has('name') ? request('name') : '');
        
        $filter_description = (request()->has('description') ? request('description') : '');

        # criação do dataset
        $dataset = new Permission;

        $dataset = $dataset->select('name', 'description');

        if (!empty($filter_name)){
            $dataset = $dataset->where('name', 'like', '%' . $filter_name . '%');    
        }

        if (!empty($filter_description)){
            $dataset = $dataset->Where('description', 'like', '%' . $filter_description . '%');
        }

        $dataset = $dataset->get();

        $pdf = PDF::loadView('admin.permissions.report', compact('dataset'));
        
        return $pdf->download('Permissoes_' .  date("Y-m-d H:i:s") . '.pdf');

    }

}
