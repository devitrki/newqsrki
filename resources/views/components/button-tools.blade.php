<button type="button" {{ $attributes->merge(['class' => 'btn btn-icon action-icon bw-thin']) }} data-toggle="tooltip" data-placement="bottom" title="{{ __($tooltip) }}">
    <span class="fonticon-wrap">
        <i class="{{ $icon }}"></i>
    </span>
</button>
