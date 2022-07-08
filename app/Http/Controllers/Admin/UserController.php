<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Perpage;
use App\Models\Role;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
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
        if (Gate::denies('user-index')) {
            abort(403, 'Acesso negado.');
        }

        $users = new User;

        // filtros
        if (request()->has('name')){
            $users = $users->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('email')){
            $users = $users->where('email', 'like', '%' . request('email') . '%');
        }

        // ordena
        $users = $users->orderBy('name', 'asc');        

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $users = $users->paginate(session('perPage', '5'))->appends([          
            'name' => request('name'),
            'email' => request('email'),           
            ]);

        return view('admin.users.index', compact('users', 'perpages'));
    }


    public function create()
    {
        if (Gate::denies('user-create')) {
            abort(403, 'Acesso negado.');
        }

        // listagem de perfis (roles)
        $roles = Role::orderBy('description','asc')->get();

        return view('admin.users.create', compact('roles'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
          'name' => 'required',
          'email' => 'required|email|unique:users,email',
          'password' => 'required|min:6|confirmed'
        ]);

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

        Session::flash('create_user', 'Operador cadastrado com sucesso!');

        return redirect(route('users.index'));
    }


    public function show($id)
    {
        // verifica o acesso
        if (Gate::denies('user-show')) {
            abort(403, 'Acesso negado.');
        }

        // usuário que será exibido e pode ser excluido
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }


    public function edit($id)
    {
        if (Gate::denies('user-edit')) {
            abort(403, 'Acesso negado.');
        }

        // usuário que será alterado
        $user = User::findOrFail($id);

        // listagem de perfis (roles)
        $roles = Role::orderBy('description','asc')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
          'name' => 'required',
          'email' => 'required|email',
        ]);

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
        $roles = $user->roles;
        if(count($roles)){
            foreach ($roles as $key => $value) {
               $user->roles()->detach($value->id);
            }
        }

        // vincula os novos perfis desse operador
        if(isset($input['roles']) && count($input['roles'])){
            foreach ($input['roles'] as $key => $value) {
               $user->roles()->attach($value);
            }
        }

        $user->update($input);
        
        Session::flash('edited_user', 'Operador alterado com sucesso!');

        return redirect(route('users.edit', $id));
    }


    public function destroy($id)
    {
        if (Gate::denies('user-delete')) {
            abort(403, 'Acesso negado.');
        }

        User::findOrFail($id)->delete();

        Session::flash('deleted_user', 'Operador excluído com sucesso!');

        return redirect(route('users.index'));
    }

public function exportcsv()
    {
        if (Gate::denies('user-export')) {
            abort(403, 'Acesso negado.');
        }

        # filtragem
        $filter_name = (request()->has('name') ? request('name') : '');
        
        $filter_email = (request()->has('email') ? request('email') : '');

        return Excel::download(new UsersExport($filter_name, $filter_email), 'Operadores_' .  date("Y-m-d H:i:s") . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportxls()
    {
        if (Gate::denies('user-export')) {
            abort(403, 'Acesso negado.');
        }

        # filtragem
        $filter_name = (request()->has('name') ? request('name') : '');
        
        $filter_email = (request()->has('email') ? request('email') : '');

        return Excel::download(new UsersExport($filter_name, $filter_email), 'Operadores_' .  date("Y-m-d H:i:s") . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportpdf()
    {
        if (Gate::denies('user-export')) {
            abort(403, 'Acesso negado.');
        }

        # tratamento dos filtros
        $filter_name = (request()->has('name') ? request('name') : '');
        
        $filter_email = (request()->has('email') ? request('description') : '');

        # criação do dataset
        $dataset = new User;

        $dataset = $dataset->select('name', 'email');

        if (!empty($filter_name)){
            $dataset = $dataset->where('name', 'like', '%' . $filter_name . '%');    
        }

        if (!empty($filter_email)){
            $dataset = $dataset->Where('email', 'like', '%' . $filter_email . '%');
        }

        $dataset = $dataset->get();

        $pdf = PDF::loadView('admin.users.report', compact('dataset'));
        
        return $pdf->download('Users_' .  date("Y-m-d H:i:s") . '.pdf');
    } 
}
