<?php
/*
Plugin Name: Latest Infinite Product Slider
Description: Add an infinite product slider to your site.
Version: 1.0
Author: Hassan Naqvi
*/
// Enqueue scripts and styles
function infinite_product_slider_scripts() {
    // GSAP
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', array(), '3.12.2', true);
    wp_enqueue_script('gsap-css-rule-plugin', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.5.1/CSSRulePlugin.min.js', array('gsap'), '3.5.1', true);


add_action('wp_enqueue_scripts', 'enqueue_jquery');

    // Stylesheet
    wp_enqueue_style('infinite-product-slider-style', plugins_url('style.css', __FILE__));

    // Custom script
    wp_enqueue_script('infinite-product-slider-script', plugins_url('script.js', __FILE__), array('gsap', 'gsap-css-rule-plugin'), '1.0', true);
}


add_action('wp_enqueue_scripts', 'infinite_product_slider_scripts');

function get_latest_woocommerce_posts($atts) {
    // Check if we are in the backend editor
    if (defined('ELEMENTOR_PATH') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
        return ''; // If in the backend editor, return an empty string
    }

    // Shortcode attributes
    $atts = shortcode_atts(
        array(
            'category' => '', // Default to empty (all categories)
            'count'    => -1, // Default to -1 (show all products)
        ),
        $atts,
        'infinite-products'
    );

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $atts['count'],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => array(),
    );

    // Add category filter if specified
    if (!empty($atts['category'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $atts['category'],
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        ob_start(); // Start output buffering
        ?>

        <section class="slider-container">
            <div id="image-container">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <div class="model-images">
                        <img class="model-imagses" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php echo get_the_title(); ?>">
                        <div class="product-title"><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></div>
                        <span class="product-category"><a href="<?php echo get_term_link(get_the_terms(get_the_ID(), 'product_cat')[0]); ?>"><?php echo get_the_terms(get_the_ID(), 'product_cat')[0]->name; ?></a></span>
                        <span class="price"><?php echo wc_price(get_post_meta(get_the_ID(), '_price', true)); ?></span>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="button-container">
                <button class="direction-button" id="left-arrow">
                    <svg fill="#000" height="24px" width="24px" viewBox="0 0 330 330">
                        <path d="M111.213,165.004L250.607,25.607c5.858-5.858,5.858-15.355,0-21.213c-5.858-5.858-15.355-5.858-21.213,0.001 l-150,150.004C76.58,157.211,75,161.026,75,165.004c0,3.979,1.581,7.794,4.394,10.607l150,149.996 C232.322,328.536,236.161,330,240,330s7.678-1.464,10.607-4.394c5.858-5.858,5.858-15.355,0-21.213L111.213,165.004z" />
                    </svg>
                </button>
                <span id="progress-bar-container"><span id="progress-bar"></span></span>
                <button class="direction-button" id="right-arrow">
                    <svg fill="#000" height="24px" width="24px" viewBox="0 0 330 330">
                        <path d="M111.213,165.004L250.607,25.607c5.858-5.858,5.858-15.355,0-21.213c-5.858-5.858-15.355-5.858-21.213,0.001 l-150,150.004C76.58,157.211,75,161.026,75,165.004c0,3.979,1.581,7.794,4.394,10.607l150,149.996 C232.322,328.536,236.161,330,240,330s7.678-1.464,10.607-4.394c5.858-5.858,5.858-15.355,0-21.213L111.213,165.004z" />
                    </svg>
                </button>
            </div>

        </section>

        <?php
        wp_reset_postdata();
        return ob_get_clean(); // Return the buffered content
    else :
        // No posts found
        return 'No WooCommerce posts found.';
    endif;
}

// Add a shortcode for easier usage in pages/posts
add_shortcode('infinite-products', 'get_latest_woocommerce_posts');








// Function to display the settings page content
function infinite_product_slider_settings_page() {
    ?>
<div class="wrap" style="font-size:20px;">
    <h1>Infinite Product Slider Settings</h1>
    
    <p style="font-size:20px">Feel free to customize the shortcode attributes based on your specific needs. Adjust the category attribute to specify a particular product category, and use the count attribute to control the number of products displayed.</p>
    
     <p style="font-size:20px">Use the [infinite-products] shortcode with optional attributes category and count to customize the displayed products. Examples:</p>

    <ul>
        <li>[infinite-products] (displays all products)</li>
        <li>[infinite-products category="electronics" count="3"] (displays 3 latest products from the "electronics" category)</li>
        <li>[infinite-products count="8"] (displays 8 latest products with no specific category)</li>
    </ul>
    
    
    <div class="video-link">
    
    <iframe width="560" height="315" src="https://www.youtube.com/embed/ycRssvOv080?si=PVGwvxC-itJYQJub" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    
    
    </div>
</div>

    
    
    <?php
}

// Function to add the settings page to the admin menu
function infinite_product_slider_add_menu() {
    add_options_page('Infinite Product Slider Settings', 'Infinite Product Slider', 'manage_options', 'infinite-product-slider-settings', 'infinite_product_slider_settings_page');
}

// Hook to add the settings page
add_action('admin_menu', 'infinite_product_slider_add_menu');

