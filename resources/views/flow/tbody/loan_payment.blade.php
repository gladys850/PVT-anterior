<td class="data-row py-5">{{ $procedure->code }}</td>
@if (!$hasSender)
<td class="data-row py-5">{{ Role::find($procedure->from_role_id)->display_name }}</td>
@endif