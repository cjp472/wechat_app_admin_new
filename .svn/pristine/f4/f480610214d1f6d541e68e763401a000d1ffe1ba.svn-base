@foreach($res_info as $key => $value )
    <tr class="{{$value->id}} membersTr" data-id="{{$value->id}}" data-membersnum="{{$value->purchase_count}} data-ismember="{{$value->is_member}}" data-name="{{$value->name}}">
        <td class="tdOne">
            @if($value->is_member==1)
                会员用户:
            @else
                专栏用户:
            @endif
            {{$value->name}}
            <input class="with-gap" name="group2" id="{{$value->id}}" type="radio"/>
            <label for="{{$value->id}}"  class="selectCheck" >
            </label>
        </td>
        <td class="memberNum">{{$value->purchase_count}}人</td>
    </tr>
@endforeach
{{var_dump($res_info)}}