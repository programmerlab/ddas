<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome!</title>
<style type="text/css">
table {
    border-collapse: collapse;
    border-color:#ccc;
     font-family:Arial, Helvetica, sans-serif ;
}
</style>
</head>
<body>
 
  <div style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000;">
        <p>Dear {{$content['first_name']??''}},</p>
        <p>Welcome !</p>
        <p>
          {!!$content['message']??''!!}
        </p>
      </p>
          <p>Best Regards,</p>
          <p>{!! $content['greeting']??'' !!}</p>
  </div>
</body>
</html>
