@if ($view == 'browse' || $view == "read")
    @if (!empty($content))
        <a href='{{$content}}' target='_blank'>View video</a>
    @else
        Not yet processed
    @endif
@elseif(in_array($view, ["edit", "add"]))
    {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
@endif
