
@foreach($res_info as $key =>$value )
    <tr class="{{$value->id}} couponsTr" data-id="{{$value->id}}" data-leftnum="{{$value->count - $value->has_received}}" data-name="{{$value->title}}">
        <td class="tdOne">
            {{$value->title}}
            <input class="with-gap" name="group2" id="{{$value->id}}" type="radio"/>
            <label for="{{$value->id}}"  class="selectCheck" >
            </label>
        </td>
        <td>{{$value->price}}</td>
        <td id="couponNum">{{$value->count - $value->has_received}}</td>
        <td>满{{$value->require_price/100}}元可用</td>
    </tr>
@endforeach