@extends('app')

@section('content')
    @if(! $data->isEmpty())
    <h2 class="text-center">Products Imported Successfully</h2>
    <table id="table_id" class="display">
        <thead>
        <tr>
            <th>OsCommerce Id</th>
            <th>WooCommerce Id</th>
            <th>Error description</th>
            <th>Date Imported</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr>
                <td>{{ $row->os_id }}</td>
                <td>{{ $row->wc_id }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->created_at->diffForHumans() }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <h2 class="text-center">No products imported</h2>
    @endif
@stop

@section('footer')
    <script>
        $(document).ready( function () {
            $('#table_id').DataTable();
        } );
    </script>
@stop
