<div>
    <!-- Mostrar las notas existentes -->
@if($task->notes->isNotEmpty())
<h2>Notas:</h2>
<ul>
    @foreach($task->notes as $note)
        <li>{{ $note->content }}</li>
    @endforeach
</ul>
@else
<p>No hay notas para esta tarea.</p>
@endif

<!-- Formulario para agregar una nueva nota -->
<form wire:submit.prevent="agregarNota">
<textarea wire:model="nuevaNota" rows="4" cols="50"></textarea>
<button type="submit">Agregar Nota</button>
</form>

</div>
