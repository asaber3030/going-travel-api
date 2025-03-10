<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>

<body>

  <form enctype="multipart/form-data" method="POST" action="{{ route('home.upload') }}">
    @csrf
    <input type="file" name="file" id="file" class="border p-2 rounded-md" />
    <input type="submit" value="Upload" class="bg-blue-500 text-white p-2 rounded-md" />
  </form>

</body>

</html>