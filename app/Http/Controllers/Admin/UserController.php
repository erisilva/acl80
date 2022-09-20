<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Perpage;
use App\Models\Role;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['middleware' => 'auth']);
        $this->middleware(['middleware' => 'hasaccess']);
    }

    public function index()
    {
        $this->authorize('user-index');

        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        return view('admin.users.index', [
            'users' => User::orderBy('name', 'asc')->filter(request(['name', 'email']))->paginate(session('perPage', '5'))->appends(request(['name', 'email'])),
            'perpages' => Perpage::orderBy('valor')->get()
        ]);
    }

    public function create()
    {
        $this->authorize('user-create');

        return view('admin.users.create', [
            'roles' => Role::orderBy('description','asc')->get() 
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
          'name' => 'required',
          'email' => 'required|email|unique:users,email',
          'password' => 'required|min:6|confirmed'
        ]);

        DB::beginTransaction();

        try{

            $user = $request->all();
            $user['active'] = 'Y'; // torna o novo registro ativo
            $user['password'] = Hash::make($user['password']); // criptografa a senha

            $newUser = User::create($user); //salva

            // salva os perfis (roles)
            if(isset($user['roles']) && count($user['roles'])){
                foreach ($user['roles'] as $key => $value) {
                    $newUser->roles()->attach($value);
                }
            }

            DB::commit();

            return redirect(route('users.index'))->with('message', 'Operador cadastrado com sucesso!');

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('users.index')->with('message','Erro ao incluir');
        }
    }

    public function show($id)
    {
        $this->authorize('user-show');
        
        return view('admin.users.show', [
            'user' => User::findOrFail($id)
        ]);
    }


    public function edit($id)
    {
        $this->authorize('user-edit');        

        return view('admin.users.edit',[
            'user' => User::findOrFail($id),
            'roles' => Role::orderBy('description','asc')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
          'name' => 'required',
          'password' => 'nullable|min:6'
        ]);

        DB::beginTransaction();

        try{
            $user = User::findOrFail($id);

            // atualiza a senha do usuário se esse campo tiver sido preenchido
            if ($request->has('password') && (request('password') != "")) {
                $input = $request->all();
                $input['password'] = Hash::make($input['password']);
            } else {
                $input = $request->except('password');
            }   

            // configura se operador está habilitado ou não a usar o sistema
            if (isset($input['active'])) {
                $input['active'] = 'Y';
            } else {
                $input['active'] = 'N';
            }

            // remove todos os perfis vinculados a esse operador
            $user->roles()->detach();

            // vincula os novos perfis desse operador
            if(isset($input['roles']) && count($input['roles'])){
                foreach ($input['roles'] as $key => $value) {
                   $user->roles()->attach($value);
                }
            }

            $user->update($input);

            DB::commit();

            return redirect(route('users.edit', $id))->with('message', 'Operador alterado com sucesso!');

        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('users.index')->with('message','Erro ao alterar dados do operador');
        }
    }

    public function destroy($id)
    {
        $this->authorize('user-delete');

        User::findOrFail($id)->delete();

        return redirect(route('users.index'))->with('message', 'Operador excluído com sucesso!');
    }

    public function exportcsv()
    {
        $this->authorize('user-export');

        return Excel::download(new UsersExport(request(['name','email'])), 'Operadores_' .  date("Y-m-d H:i:s") . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportxls()
    {
        $this->authorize('user-export');

        return Excel::download(new UsersExport(request(['name','email'])), 'Operadores_' .  date("Y-m-d H:i:s") . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportpdf()
    {
        $this->authorize('user-export');

        return PDF::loadView('admin.users.report', [
            'dataset' => User::orderBy('name', 'asc')->filter(request(['name', 'email']))->get()
        ])->download('Users_' .  date("Y-m-d H:i:s") . '.pdf');
    } 
}
