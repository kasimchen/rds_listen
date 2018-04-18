<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>

@if($data)
    @foreach($data as $item)

<div class="Section1" style='layout-grid:15.6pt'>

    <div style="text-align:center;margin:30px 0 0 0;">
        <p align="justify" class="MsoNormal" style="text-align:center;">
            <b><span style='font-size:14.0pt;font-family:宋体;color:black'>用户高峰查询日志 ({{$item['ReportTime']}})</span></b>
        </p>
    </div>

    <div style="text-align:center;margin:30px 0 0 0;">
        <p align="justify" class="MsoNormal" style="width: 80%;margin: 0px auto;">
        <table style="border:solid black 1.0pt;border-bottom: none;" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th style='border-right: solid black 1.0pt;'>查询次数</th>
                <th>sql语句</th>
            <tr>
            </thead>
            <tbody>

                @foreach($item['qpstop'] as $i)

                    <tr>
                        <td style='border:solid black 1.0pt;border-left: none;width: 10%;text-align: center;'>
                            {{$i['SQLExecuteTimes']}}
                        </td>
                        <td style='border:solid black 1.0pt;border-left: none;width: 90%;text-align: center;'>
                            {{$i['sql']}}
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    </div>

    @endforeach
@endif

</div>
</body>
</html>