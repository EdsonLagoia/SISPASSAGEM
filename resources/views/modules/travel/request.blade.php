<label for="{{ $travel }}_request" class="form-label">Numero da Requisição <span class="text-danger fw-bold {{ $travel }}">*</span></label>
<input type="text" class="form-control {{ $travel }}s" id="{{ $travel }}_request" name="{{ $travel }}_request" value="{{ $number }}" required>

<script>$('#{{ $travel }}_request').mask('0000');</script>