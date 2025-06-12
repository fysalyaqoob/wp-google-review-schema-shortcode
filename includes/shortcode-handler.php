<?php
global $google_review_schema_blocks;
$google_review_schema_blocks = [];

add_shortcode('google_review_schema', function($atts) {
    global $google_review_schema_blocks;

    $atts = shortcode_atts([
        'place_id'    => '',
        'name'        => '',
        'description' => '',
        'phone'       => '',
        'street'      => '',
        'locality'    => '',
        'region'      => '',
        'postal'      => '',
        'country'     => '',
        'webapp'      => 'false'
    ], $atts);

    $api_key = defined('GOOGLE_PLACES_API_KEY') ? GOOGLE_PLACES_API_KEY : '';
    if (!$atts['place_id'] || !$api_key) return '';

    $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$atts['place_id']}&fields=name,rating,user_ratings_total,reviews,url,formatted_address,international_phone_number,address_components&key={$api_key}";
    $response = wp_remote_get($url);
    if (is_wp_error($response)) return '';
    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($data['result'])) return '';

    $result  = $data['result'];
    $rating  = floatval($result['rating']);
    $total   = intval($result['user_ratings_total']);
    $reviews = $result['reviews'] ?? [];
    $address_components = $result['address_components'] ?? [];

    $street   = $atts['street'];
    $locality = $atts['locality'];
    $region   = $atts['region'];
    $postal   = $atts['postal'];
    $country  = $atts['country'];

    foreach ($address_components as $component) {
        $types = $component['types'];
        if (in_array('street_number', $types)) $street = $component['long_name'] . ' ' . $street;
        if (in_array('route', $types)) $street = $street ? "$street " . $component['long_name'] : $component['long_name'];
        if (in_array('locality', $types)) $locality = $component['long_name'];
        if (in_array('administrative_area_level_1', $types)) $region = $component['short_name'];
        if (in_array('postal_code', $types)) $postal = $component['long_name'];
        if (in_array('country', $types)) $country = $component['short_name'];
    }

    $schema = [
        "@context" => "https://schema.org",
        "@type" => "LocalBusiness",
        "name" => $atts['name'] ?: $result['name'],
        "description" => $atts['description'] ?: '',
        "telephone" => $atts['phone'] ?: ($result['international_phone_number'] ?? ''),
        "url" => get_permalink(),
        "image" => get_site_url() . '/wp-content/uploads/2020/01/cropped-ccs-logo-symbol.png',
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => $street,
            "addressLocality" => $locality,
            "addressRegion" => $region,
            "postalCode" => $postal,
            "addressCountry" => $country,
        ],
        "aggregateRating" => [
            "@type" => "AggregateRating",
            "ratingValue" => $rating,
            "reviewCount" => $total
        ],
        "review" => []
    ];

    foreach ($reviews as $r) {
        if (intval($r['rating']) !== 5) continue;
        $schema['review'][] = [
            "@type" => "Review",
            "author" => [
                "@type" => "Person",
                "name" => esc_html($r['author_name']),
            ],
            "reviewRating" => [
                "@type" => "Rating",
                "ratingValue" => 5,
                "bestRating" => 5,
                "worstRating" => 1,
            ],
            "reviewBody" => esc_html($r['text']),
            "datePublished" => date('c', $r['time'])
        ];
    }

    $google_review_schema_blocks[] = wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($atts['webapp'] === 'true') {
        $webapp_schema = [
            "@context" => "https://schema.org",
            "@type" => "WebApplication",
            "name" => $result['name'],
            "applicationCategory" => "FinanceApplication",
            "applicationSubCategory" => "Credit",
            "aggregateRating" => [
                "@type" => "AggregateRating",
                "ratingValue" => $rating,
                "bestRating" => 5,
                "reviewCount" => $total
            ],
            "offers" => [
                "@type" => "Offer",
                "price" => "0",
                "priceCurrency" => "CAD"
            ],
            "operatingSystem" => "All",
            "browserRequirements" => "Requires Javascript. Requires HTML5",
            "inLanguage" => "EN",
            "author" => [
                "@type" => "Organization",
                "name" => $result['name'],
                "url" => home_url()
            ],
            "creator" => [
                "@type" => "Organization",
                "name" => $result['name'],
                "url" => home_url()
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => $result['name'],
                "url" => home_url()
            ],
            "description" => $atts['description'] ?: "Nonprofit credit counseling services offered by {$result['name']}.",
            "softwareVersion" => "1.0.0",
            "screenshot" => get_site_url() . '/screenshot.png',
            "permissions" => "Full Internet Access",
            "isFamilyFriendly" => "true"
        ];
        $google_review_schema_blocks[] = wp_json_encode($webapp_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    return '';
});

add_action('wp_footer', function() {
    global $google_review_schema_blocks;
    foreach ($google_review_schema_blocks as $json) {
        echo '<script type="application/ld+json">' . $json . '</script>';
    }
}, 100);
