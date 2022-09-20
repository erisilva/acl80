<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Perpage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $this->authorize('permission-index');

        // atualiza perPage se necessário
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        return view('admin.permissions.index', [
            'permissions' => Permission::orderBy('id', 'asc')->filter(request(['name', 'description']))->paginate(session('perPage', '5'))->appends(request(['name', 'description'])),
            'perpages' => Perpage::orderBy('valor')->get()
        ]);
    }

    public function create()
    {
        $this->authorize('permission-create');

        return view('admin.permissions.create');
    }


    public function store(Request $request)
    {
        // Nota: o $request->validate([...]) retorna só os campos que forem validados na lista
        // usar o $request->all(); para pegar todos os campos
        $permission = $request->validate([
          'name' => 'required',
          'description' => 'required',
        ]);

        Permission::create($permission);

        return redirect(route('permissions.index'))->with('message', 'Permissão cadastrada com sucesso!');
    }

    public function show($id)
    {
        $this->authorize('permission-show');

        return view('admin.permissions.show', [
            'permission' => Permission::findOrFail($id)
        ]);
    }

    public function edit($id)
    {
        $this->authorize('permission-edit');

        return view('admin.permissions.edit', [
            'permission' => Permission::findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $permission = $request->validate([
          'name' => 'required',
          'description' => 'required',
        ]);

        Permission::findOrFail($id)->update($permission);

        return redirect(route('permissions.edit', $id))->with('message', 'Permissão alterada com sucesso!');
    }

    public function destroy($id)
    {
        $this->authorize('permission-delete');
        
        Permission::findOrFail($id)->delete();

        return redirect(route('permissions.index'))->with('message', 'Permissão excluída com sucesso!');
    }

    public function exportcsv()
    {
        $this->authorize('permission-export');

        return Excel::download(new PermissionsExport(request(['name', 'description'])), 'Permissoes_' .  date("Y-m-d H:i:s") . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportxls()
    {
        $this->authorize('permission-export');

        return Excel::download(new PermissionsExport(request(['name', 'description'])), 'Permissoes_' .  date("Y-m-d H:i:s") . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportpdf()
    {
        $this->authorize('permission-export');
        
        return PDF::loadView('admin.permissions.report', [
            'dataset' => Permission::orderBy('id', 'asc')->filter(request(['name', 'description']))->get()
        ])->download('Permissoes_' .  date("Y-m-d H:i:s") . '.pdf');
    }

}
