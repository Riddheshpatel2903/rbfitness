<div class="plans-grid">
    @forelse($category->plans as $pIndex => $plan)
    <div class="plan-card {{ $pIndex == 1 ? 'featured' : '' }}">
        @if($pIndex == 1)
        <span class="plan-badge">MOST POPULAR</span>
        @endif
        <div class="plan-header">
            <h3 class="plan-name">{{ $plan->name }}</h3>
            <div class="plan-price">
                <span class="plan-price-value">₹{{ number_format($plan->price, 0) }}</span>
                <span class="plan-price-period">/{{ $plan->duration_days }} days</span>
            </div>
            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.4); margin-top: 0.2rem; font-weight: 500;">
                + ₹200 Joining Fees (First time only)
            </div>
        </div>
        <ul class="plan-features">
            @if($plan->description)
                @foreach(explode("\n", str_replace("\r", "", $plan->description)) as $feature)
                    @if(trim($feature))
                    <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" /></svg>{{ trim($feature) }}</li>
                    @endif
                @endforeach
            @elseif($plan->features)
                @foreach(json_decode($plan->features) ?? [] as $feature)
                    <li><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" /></svg>{{ $feature }}</li>
                @endforeach
            @endif
        </ul>
        <button class="btn-plan primary" onclick="redirectToWhatsApp('{{ $plan->name }} Membership')">CONTACT US</button>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; color: rgba(255,255,255,0.5); padding: 4rem;">
        No plans found for this category.
    </div>
    @endforelse
</div>
