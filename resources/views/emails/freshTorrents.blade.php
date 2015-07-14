<!DOCTYPE html>
<html>
<body>
<div class="container">
    <div class="content">
        <div class="title">Новые торренты за <?=date("Y-m-d")?></div>
        @if($torrents)
            <table>
                <thead>
                <th>Название</th>
                <th>Сидеры</th>
                <th>Личеры</th>
                <th>Скачано</th>
                </thead>
            <tbody>
            @foreach($torrents as $torrent)
                <tr>
                    <td><a href="{{$torrent->downloadLink}}">{{$torrent->name}}</a></td>
                    <td>{{$torrent->seeders}}</td>
                    <td>{{$torrent->leechers}}</td>
                    <td>{{$torrent->downloadTimes}}</td>
                </tr>
            @endforeach
            </tbody>
            </table>
        @endif
    </div>
</div>
</body>
</html>
