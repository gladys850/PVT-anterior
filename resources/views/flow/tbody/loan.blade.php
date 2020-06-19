<td class="data-row py-5">{{ $procedure->code }}</td>
<td class="data-row py-5">{{ $procedure->disbursable->title }} {{ $procedure->disbursable->full_name }}</td>
<td class="data-row py-5">{{ $procedure->disbursable->identity_card_ext }}</td>
@php ($created_at = Carbon::parse($procedure->created_at))
<td class="data-row py-5">{{ $created_at->isoFormat('L') }} {{ $created_at->toTimeString() }}</td>
<td class="data-row py-5">{{ Util::money_format($procedure->amount_approved) }}</td>
<td class="data-row py-5">{{ $procedure->loan_term }} Mes{{ $procedure->loan_term > 1 ? 'es' : '' }}</td>
<td class="data-row py-5">{{ $procedure->city->name }}</td>
@if (!$hasSender)
<td class="data-row py-5">{{ Role::find($procedure->from_role_id)->display_name }}</td>
@endif