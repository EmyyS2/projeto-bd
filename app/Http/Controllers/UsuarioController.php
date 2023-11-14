<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class UsuarioController extends Controller
{
    public function store(UsuarioRequest $request)
    {
        $usuario = Usuario::create([
            'nome' => $request->nome,
            'cpf' => $request->cpf,
            'celular' => $request->celular,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return response()->json([
            "success" => true,
            "message" => "Usuario Cadastrado com sucesso",
            "data" => $usuario
        ]);
    }
    public function pesquisarPorId($id)
    {
        $usuario = Usuario::find($id);

        if ($usuario == null) {
            return response()->json([
                'status' => false,
                'message' => "Usuário não encontrado"
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $usuario
        ]);
    }
    public function pesquisarPorCpf($cpf)
    {
        $usuario = Usuario::where('cpf', '=', $cpf)->first();

        if ($usuario == null) {
            return response()->json([
                'status' => false,
                'message' => "Usuário não encontrado"
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $usuario
        ]);
    }

    public function retornarTodos()
    {
        $usuarios = Usuario::all();
        return response()->json([
            'status' => true,
            'data' => $usuarios
        ]);
    }

    public function pesquisarPorNome(Request $request)
    {
        $usuarios = Usuario::where('nome', 'like', '%' . $request->nome . '%')->get();

        if (count($usuarios) > 0) {

            return response()->json([
                'status' => true,
                'data' => $usuarios
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Não há resultados para pesquisa.'
        ]);
    }

    public function excluir($id)
    {
        $usuario = Usuario::find($id);
        if (!isset($usuario)) {
            return response()->json([
                'status' => false,
                'message' => "Usuario não encontrado"
            ]);
        }

        $usuario->delete();

        return response()->json([
            'status' => true,
            'message' => "usuário excluído com sucesso"
        ]);
    }

    public function update(Request $request)
    {
        $usuario = Usuario::find($request->id);
        if (!isset($usuario)) {
            return response()->json([
                'status' => false,
                'message' => "Usuario não encontrado"
            ]);
            if (!isset($request->email)) {
                $usuario->email = $request->email;
            }
            if (!isset($request->nome)) {
                $usuario->nome = $request->nome;
            }

            if (!isset($request->cpf)) {
                $usuario->cpf = $request->cpf;
            }
        }
        $usuario->update();

        return response()->json([
            'status' => true,
            'message' => "usuário atualizado"
        ]);
    }
    public function exportarCsv()
    {
        $usuarios = Usuario::all();
        $nomeArquivo = 'usuarios.csv';
        $filePath = storage_path('app/public/' . $nomeArquivo);
        $handle = fopen($filePath, "w");
        fputcsv($handle, array('Nome', ' Email', " CPF"), ';');
        foreach($usuarios as $u){
            fputcsv($handle, array(
                $u -> nome,
                $u -> email,
                $u -> cpf
            ), ';');
        }
        fclose($handle);
        return Response::download(public_path().'/storage/'.$nomeArquivo)  ->deleteFileAfterSend(true);
    }
}
