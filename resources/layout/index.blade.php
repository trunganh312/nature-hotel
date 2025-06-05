<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" id="viewport" />
    <meta name="robots" content="NOINDEX" />
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon" />
    
    <title>{{ $title }}</title>

    <script>var domain_user = "{{ $domain_user }}";</script>
    @foreach ($js as $file)
        <script type="text/javascript" src="{{ $file }}"></script>
    @endforeach
    <script>
    </script>
    @foreach ($css as $file)
        <link rel="stylesheet" href="{{ $file }}" />
    @endforeach
   
    <script>window.appData = <?= $data_json?>;</script>
</head>
<body>
    <div id="app">
        <a-config-provider :locale="locale">
            <layout-index :scrence="('{{ $scrence }}')" :title="('{{ $title }}')"></layout-index>
        </a-config-provider>
    </div>
    <script type="text/javascript">
        window.app({});
    </script>
</body>

</html>