<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reminder;
use App\Http\Resources\ReminderResource;

class ReminderController extends Controller
{
    // Listar recordatorios del usuario autenticado
    public function index()
    {
        $userId = Auth::id();

        $items = Reminder::where('user_id', $userId)
            ->latest('remind_at')
            ->get();

        return ReminderResource::collection($items);
    }

    // Crear nuevo recordatorio
    public function store(Request $request)
    {
        $data = $request->validate([
        'title'       => ['required','string','max:255'],
        'description' => ['nullable','string'],
        'remind_at'   => ['required','date'],
    ]);

    $data['user_id']  = Auth::id();
    $data['notified'] = false;

    $reminder = Reminder::create($data);
    return (new ReminderResource($reminder))->response()->setStatusCode(201);

    }

    // Mostrar recordatorio específico
    public function show(Reminder $reminder)
    {
        $this->authorizeReminder($reminder);

        return new ReminderResource($reminder);
    }

    // Actualizar recordatorio
    public function update(Request $request, Reminder $reminder)
    {
        $this->authorizeReminder($reminder);

        $input = $request->all();
        if (!isset($input['remind_at']) && isset($input['reminder_date'])) {
            $input['remind_at'] = $input['reminder_date'];
        }

        $data = validator($input, [
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'remind_at'   => ['sometimes', 'required', 'date'], // opcional: 'after:now'
            'notified'    => ['sometimes', 'boolean'],
        ], [], [
            'remind_at'   => 'fecha del recordatorio',
        ])->validate();

        $reminder->update($data);

        return new ReminderResource($reminder);
    }

    // Eliminar recordatorio
    public function destroy(Reminder $reminder)
    {
        $this->authorizeReminder($reminder);

        $reminder->delete();

        return response()->json(['message' => 'Recordatorio eliminado']);
    }

    // Verifica que el recordatorio pertenezca al usuario
    private function authorizeReminder(Reminder $reminder): void
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
    }
}
