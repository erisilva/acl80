<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromQuery, WithHeadings
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    * 
    * php artisan make:export PermissionsExport --model=Permission
    * 
    * https://laravel-excel.com/
    * 
    */

    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
    }


    public function query()
    {
        $result = User::query()->select('name', 'email');

        if (!empty($this->name)){
            $result = $result->where('name', 'like', '%' . $this->name . '%');    
        }

        if (!empty($this->email)){
            $result = $result->Where('email', 'like', '%' . $this->email . '%');
        }


        return $result;
    }

    public function headings(): array
    {
        return ["Nome", "E-mail"];
    }
}
