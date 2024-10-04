<table>
    <thead>
        <tr>
            @foreach($exportCols as $col)

                <th>{{ $col }}</th>

            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($options as $option)
            <tr>
                @foreach($exportCols as $col)

                    <td>{{ $option->$col }}</td>

                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>