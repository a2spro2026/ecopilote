@if ($code === 'fr')
    <svg viewBox="0 0 3 2" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
        <rect width="1" height="2" x="0" fill="#002395"/>
        <rect width="1" height="2" x="1" fill="#fff"/>
        <rect width="1" height="2" x="2" fill="#ed2939"/>
    </svg>
@elseif ($code === 'gb')
    <svg viewBox="0 0 60 30" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
        <rect width="60" height="30" fill="#012169"/>
        <path d="M0 0 L60 30 M60 0 L0 30" stroke="#fff" stroke-width="6"/>
        <path d="M0 0 L60 30 M60 0 L0 30" stroke="#C8102E" stroke-width="2"/>
        <path d="M30 0 V30 M0 15 H60" stroke="#fff" stroke-width="10"/>
        <path d="M30 0 V30 M0 15 H60" stroke="#C8102E" stroke-width="6"/>
    </svg>
@elseif ($code === 'ma')
    <svg viewBox="0 0 900 600" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
        <rect width="900" height="600" fill="#c1272d"/>
        <polygon fill="none" stroke="#006233" stroke-width="28"
                 points="450,180 513,372 357,254 543,254 387,372"/>
    </svg>
@endif
