<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudante;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EstudanteController extends Controller
{


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string',
            'endereco' => 'required|string',
            'telefone' => 'required|string',
            'foto' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $estudante = new Estudante();
        $estudante->nome = $request->nome;
        $estudante->endereco = $request->endereco;
        $estudante->telefone = $request->telefone;

        if ($request->has('foto')) {
            $image = $request->get('foto');
            $imageName = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Storage::disk('public')->put('fotos/' . $imageName, base64_decode(explode(',', $image)[1]));
            $estudante->foto = 'storage/fotos/' . $imageName;
        }

        $estudante->save();

        return response()->json(['message' => 'Aluno criado com sucesso!', 'data' => $estudante], 201);
    }

//scp -r /home/sam/Documentos/CienciaDeDados/Challengs/CrudEstudante@31.220.31.176:/root/desafio


public function index()
{
    $estudantes = Estudante::all();
    return response()->json($estudantes);
}

public function show($id)
{
    $estudante = Estudante::find($id);

    if (!$estudante) {
        return response()->json(['error' => 'Estudante não cadastrado'], 404);
    }

    return response()->json($estudante);
}

public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'nome' => 'required|string',
        'endereco' => 'required|string',
        'telefone' => 'required|string',
        'foto' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $estudante = Estudante::find($id);

    if (!$estudante) {
        return response()->json(['error' => 'Student not found'], 404);
    }

    $estudante->nome = $request->nome;
    $estudante->endereco = $request->endereco;
    $estudante->telefone = $request->telefone;

    if ($request->foto) {
        if ($estudante->foto) {
            Storage::delete(str_replace('/storage', 'public', $estudante->foto));
        }

        $image = $request->foto;
        $imageName = time() . '_' . uniqid() . '.jpg';
        Storage::put('public/fotos/' . $imageName, base64_decode($image));
        $estudante->foto = Storage::url('public/fotos/' . $imageName);
    }

    $estudante->save();

    return response()->json(['message' => 'Student updated successfully', 'data' => $estudante]);
}


public function destroy($id)
{
    $estudante = Estudante::find($id);

    if (!$estudante) {
        return response()->json(['error' => 'Estudante não cadastrado'], 404);
    }

    if ($estudante->foto) {
        Storage::delete(str_replace('/storage', 'public', $estudante->foto));
    }

    $estudante->delete();

    return response()->json(['message' => 'Estudante apagado']);
}



}
