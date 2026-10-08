<tr><td><select name="items[{{ $n }}][kind]"><option value="main" @selected($it->kind==='main')>Unit</option><option value="extra" @selected($it->kind==='extra')>Tambahan</option></select></td>
<td><textarea name="items[{{ $n }}][mobil_text]">{{ $it->mobil_text }}</textarea></td><td><textarea name="items[{{ $n }}][rute_text]">{{ $it->rute_text }}</textarea></td>
<td><input type="number" min="0" name="items[{{ $n }}][qty]" value="{{ $it->qty }}"></td><td><input type="number" min="0" name="items[{{ $n }}][days]" value="{{ $it->days }}"></td>
<td><input type="number" min="0" name="items[{{ $n }}][price]" value="{{ $it->price }}"></td><td><input type="number" min="0" name="items[{{ $n }}][amount]" value="{{ $it->exists?$it->amount:'' }}"></td>
<td><button type="button" class="link-danger" title="Hapus baris" onclick="this.closest('tr').remove()">✕</button></td></tr>
