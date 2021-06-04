<td class="data-row py-5">{{ $procedure->code }}</td>
<td class="data-row py-5">{{ $procedure->estimated_quota }} </td>
<td class="data-row py-5">{{ $procedure->quota_number }}</td>
<td class="data-row py-5">{{ $procedure->estimated_date }} </td>
@php ($created_at = Carbon::parse($procedure->created_at))
<td class="data-row py-5">{{ $created_at->isoFormat('L') }} {{ $created_at->toTimeString() }}</td>
@if (!$hasSender)
<td class="data-row py-5">{{ Role::find($procedure->from_role_id)->display_name }}</td>
@endif