<label for="companion2" class="form-label">Acompanhante 2:</label>
<select class="form-select" id="companion2" name="companion2" {{ $companion ? '' : 'disabled' }}>
    <option value="">---</option>
    @if($companion)
        @foreach($companion as $companion)
            <option value="{{ $companion->id }}">{{ $companion->name }}</option>
        @endforeach
    @endif
</select>
