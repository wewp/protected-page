<?php

class Wewp_Utils
{
    public static function insertRows($wp_table_name, $row_arrays)
    {
        global $wpdb;
        $wp_table_name = esc_sql($wp_table_name);
        // Setup arrays for Actual Values, and Placeholders
        $values = array();
        $place_holders = array();
        $query = "";
        $query_columns = "";

        $query .= "INSERT INTO {$wp_table_name} (";

        foreach ($row_arrays as $count => $row_array) {

            foreach ($row_array as $key => $value) {

                if ($count == 0) {
                    if ($query_columns) {
                        $query_columns .= "," . $key . "";
                    } else {
                        $query_columns .= "" . $key . "";
                    }
                }

                $values[] = $value;

                if (is_numeric($value)) {
                    if (isset($place_holders[$count])) {
                        $place_holders[$count] .= ", '%d'";
                    } else {
                        $place_holders[$count] = "( '%d'";
                    }
                } else {
                    if (isset($place_holders[$count])) {
                        $place_holders[$count] .= ", '%s'";
                    } else {
                        $place_holders[$count] = "( '%s'";
                    }
                }
            }
            // mind closing the GAP
            $place_holders[$count] .= ")";
        }

        $query .= " $query_columns ) VALUES ";

        $query .= implode(', ', $place_holders);

        if ($wpdb->query($wpdb->prepare($query, $values))) {
            return true;
        } else {
            return false;
        }
    }

    public static function getIP()
    {
        $ip = null;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public static function is_post_type_support($post_type)
    {
        if (!post_type_exists($post_type)) {
            return false;
        }

        if (!post_type_supports($post_type, 'wewp-protected-page')) {
            return false;
        }

        return true;
    }

    public static function getOptionAdminTopBar()
    {
        return get_option('wewp_protected_page_top_bar_button', 1);
    }

    public static function isDisplayAdminTopBarButton()
    {
        return self::getOptionAdminTopBar() === '1';
    }
}
