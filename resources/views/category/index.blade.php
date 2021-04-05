@extends('adminlte::page')

@section('title', 'Category List')

@section('css')
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
@stop

@section('content_header')
    <h1>Category List</h1>
@stop

@section('content')

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="float-left">
                    <a href="{{ route('categories.create') }}" title="Add New Category">Add New Category</a>
                </div>
                <div class="float-right">  
                    <form id="mltplDltFrm" method="POST" action="{{route('categories.destroy-all')}}">  
                        @csrf
                        <input type="hidden" id="dids" name="ids">    
                        <input type="button" id="deleteSeleted" class="btn btn-danger" value="Delete">
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th class="text-center"><input type="checkbox" id="chkall" name="chkall"></th>
                            <th>Name</th>
                            <th>Image</th>
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {!! $categories !!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script>
$(function () {
     
    $("#chkall").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    var table = $('.data-table').DataTable({
        columnDefs: [
            { orderable: false, targets: [0,2,3] },
            { searchable: false, targets: [0,2,3] }
        ],
        order: [[1, 'asc']]
    });

    $('body').on('click', '#deleteSeleted', function () {
      
        var ids = [];
        $("input:checkbox[name=chkProduct]:checked").each(function(){
            ids.push($(this).val());
        });
        if (ids.length === 0) {
            alert('Please select at least one checkbox to delete.');
        }else{
            $('#dids').val(ids);
            var r = confirm("Are you sure want to delete!");
            if (r == true) {
                $('#mltplDltFrm').submit();
            }
        }
    });
});

function deleteRow(id)
{   
    var r = confirm("Are you sure want to delete!");
    if (r == true) {
        $('#deletefrm_'+id).submit();
    }
}
</script>
@stop
