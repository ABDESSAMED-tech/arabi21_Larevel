@if ($view == 'browse')
    @livewire('toggle-button', [
      'model' => $data,
      'field' => 'active'
    ])

@elseif($view == "read")
    {{ $content == 1 ? 'Active' : 'Inactive' }}
@elseif(in_array($view, ["edit", "add"]))
    {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
@endif

