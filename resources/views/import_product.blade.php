<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ \Settings::get('site_name','Corals') }}</title>
    <link rel="shortcut icon" href="{{ \Settings::get('site_favicon') }}" type="image/png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <h2>Import Products</h2>
        <div class="col-md-12">
            <a href="{{url('')}}" class="btn btn-info btn-sm pull-right" style="">Download Product Csv</a>
        <br/>
            <form method="post" action="{{url('marketplace/products/store-import-product')}}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{csrf_token()}}"/>
                <div class="form-group">
                    <label>Upload File</label>
                    <input type="file" name="product_file" class="form-control" required/>
                </div>
                <button type="submit" class="btn bnt-info">Submit</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
