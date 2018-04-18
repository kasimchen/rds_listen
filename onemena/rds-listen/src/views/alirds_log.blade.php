<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div class="Section1" style='layout-grid:15.6pt'>

    <div style="text-align:center;margin:30px 0 0 0;">
        <p align="justify" class="MsoNormal" style="text-align:center;">
            <b><span style='font-size:14.0pt;font-family:宋体;color:black'>慢查询日志 ({{$time}})</span></b>
        </p>
    </div>

    <div style="text-align:center;margin:30px 0 0 0;">
        <p align="justify" class="MsoNormal" style="width: 80%;margin: 0px auto;">
        <table style="border:solid black 1.0pt;border-bottom: none;" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th style='border-right: solid black 1.0pt;'>数据库名</th>
                <th style='border-right: solid black 1.0pt;'>单条查询时间</th>
                <th style='border-right: solid black 1.0pt;'>查询次数</th>
                <th style='border-right: solid black 1.0pt;'>总查询时间</th>
                <th>sql语句</th>
            <tr>
            </thead>
            <tbody>
            @if($data)
            @foreach($data as $item)
            <tr>
                <td style='border:solid black 1.0pt;border-left: none;width: 100px;text-align: center;'>
                    {{$item['DBName']}}
                </td>
                <td style='border:solid black 1.0pt;border-left: none;width: 100px;text-align: center;'>
                    {{$item['MaxExecutionTime']}}
                </td>
                <td style='border:solid black 1.0pt;border-left: none;width: 100px;text-align: center;'>
                    {{$item['MySQLTotalExecutionCounts']}}
                </td>
                <td style='border:solid black 1.0pt;border-left: none;width: 100px;text-align: center;'>
                    {{$item['MySQLTotalExecutionTimes']}}
                </td>

                <td style='border:solid black 1.0pt;border-left: none;border-right: none;text-align: center;min-width: 300px;'>
                    {{$item['SQLText']}}
                </td>
            </tr>
            @endforeach
            @endif
            </tbody>
        </table>
    </div>

</div>
</body>
</html>