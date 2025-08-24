<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate(
                [
                    'name'     => 'required|string|max:100',
                    'email'    => 'required|email:rfc|max:100|unique:users,email',
                    'password' => 'required|string|min:6',
                ],
                [
                    'email.unique' => 'El correo ya está registrado.',
                    'email.email'  => 'El correo no parece válido.',
                ]
            );

            $data['email'] = Str::lower($data['email']);

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
                'is_admin' => 0,
            ]);

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'user'    => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validación fallida',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error de base de datos',
            ], 500);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }
}
