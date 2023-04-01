<table>
    <tr>
        <td colspan="8"><strong>Outlet :</strong> {{$data['desc']['outlet']}}</td>
    </tr>
    <tr>
        <td colspan="8"><strong>Date :</strong> {{$data['desc']['date']}}</td>
    </tr>
    <tr>
        <td colspan="8"><strong>MOD :</strong> {{$data['desc']['mod']}}</td>
    </tr>
    <tr>
        <td colspan="8"><strong>Crew Opening :</strong> {{$data['desc']['crew_opening']}}</td>
    </tr>
    <tr>
        <td colspan="8"><strong>Crew Mid Night :</strong> {{$data['desc']['crew_midnight']}}</td>
    </tr>
    <tr>
        <td colspan="8"><strong>Crew Closing :</strong> {{$data['desc']['crew_closing']}}</td>
    </tr>
</table>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>OUM</th>
        <th>Frekuensi</th>
        <th>Opening Stock</th>
        <th>Stock In</th>
        <th>Stock Out</th>
        <th>Closing Stock</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data['table'] as $i => $t)
    <tr>
        <td>{{$i+1}}</td>
        <td>{{$t->name}}</td>
        <td>{{$t->uom}}</td>
        <td>{{$t->frekuensi}}</td>
        <td align="right">{{$t->stock_opening}}</td>
        <td align="right">{{$t->stock_in}}</td>
        <td align="right">{{$t->stock_out}}</td>
        <td align="right">{{$t->stock_closing}}</td>
    </tr>
    @endforeach
    </tbody>
</table>