@extends('adminlte::page')

@section('title', 'Product List')

@section('css')
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
@stop

@section('content_header')
    <h1>Product List</h1>
@stop

@section('content')

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="mb-3">
                    <a href="{{ route('products.create') }}" title="Add New Product">Add New Product</a>
                </div>
                <div class="float-left">
                    <form class="form-inline">
                        <label class="mr-sm-2">Search:</label>
                        <input type="text" class="form-control mr-sm-2" id="name" name="name" placeholder="Enter Product Name">
                        <select id="category" name="category" class="form-control mr-sm-2">
                            <option value="">Select Category</option>
                        </select>
                        <button type="button" id="fltrBtn" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="float-right">        
                    <input type="button" id="deleteSeleted" class="btn btn-danger" value="Delete">
                </div>
            </div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <div id="ajaxMsg"></div>
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th class="text-center"><input type="checkbox" id="chkall" name="chkall"></th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('js/category-dropdown.js') }}"></script>
<script>
$(function () {
    
    var category = "{{old('category')}}";
    var url = "{{ route('get-categories-dropdown') }}";
    setTimeout(function(){ getCategoriesDropDown(url, 'category', category); }, 300);

    $("#chkall").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        //ajax: "{{ route('products.index') }}",
        ajax: {
            url: "{{ route('products.index') }}",
            data: function (d) {
                d.name = $('#name').val(),
                d.category = $('#category').val(),
                d.search = $('input[type="search"]').val()
            }
        },
        order: [[1, 'asc']],
        columns: [
            {data: 'checkbox', name: 'checkbox', className: 'text-center', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'image', name: 'image', orderable: false, searchable: false},
            {data: 'category', name: 'category'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $('body').on('click', '.deleteProduct', function () {
     
        var product_id = $(this).data("id");
        var r = confirm("Are you sure want to delete!");
        if (r == true) {
            $.ajax({
                type: "DELETE",
                url: "{{ url('products') }}/"+product_id,
                data: {"_token": "{{ csrf_token() }}"},
                success: function (data) {
                    if(data.status == 200){
                        $('.alert').remove();
                        $('#ajaxMsg').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><p>'+data.msg+'</p></div>');
                    }
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    });

    $('body').on('click', '#deleteSeleted', function () {
      
        var ids = [];
        $("input:checkbox[name=chkProduct]:checked").each(function(){
            ids.push($(this).val());
        });

        var r = confirm("Are you sure want to delete!");
        if (r == true) {
            $.ajax({
                type: "POST",
                url: "{{ route('products.destroy-all') }}",
                data: {"_token": "{{ csrf_token() }}", "ids" : ids},
                success: function (data) {
                    if(data.status == 200){
                        $('.alert').remove();
                        $('#ajaxMsg').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><p>'+data.msg+'</p></div>');
                    }
                    $('#chkall').prop('checked', false);
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    });

    $('body').on('click', '#fltrBtn', function () {

        table.draw();
    });

});
</script>
@stop
