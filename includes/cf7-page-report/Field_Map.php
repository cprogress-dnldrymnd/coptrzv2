<?php
/**
 * Plugin/Snippet Name: CF7 Page Report — Field Map
 * Description: Maps the many different CF7 field names in use across this
 *              site's forms (your-email, email, email-address, ...) onto a
 *              small set of canonical columns (Email, Name, Phone, Message)
 *              for the cross-form detail view. The report can't have one
 *              column per possible field name across dozens of forms, so a
 *              two-tier model applies: canonical columns from this map, plus
 *              every other field preserved in a per-row expander so nothing
 *              is lost. Filterable via `coptrz_cf7_field_aliases` since this
 *              site has many forms and the aliases will need site-specific
 *              tuning over time.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

namespace CoptrzTheme\CF7PageReport;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Resolves canonical Email/Name/Phone/Message values from a submission's
 * raw, per-form field set.
 */
class Field_Map
{
    /**
     * Default alias lists, keyed by canonical column. Comparisons are
     * case-insensitive and ignore leading/trailing underscores and hyphens.
     *
     * @var array<string,string[]>
     */
    const DEFAULT_ALIASES = array(
        'email'      => array('your-email', 'email', 'email-address', 'e-mail'),
        'name'       => array('your-name', 'name', 'full-name', 'fullname'),
        'first_name' => array('first-name', 'firstname'),
        'last_name'  => array('last-name', 'lastname'),
        'phone'      => array('your-phone', 'tel', 'phone', 'telephone', 'phone-number'),
        'message'    => array('your-message', 'message', 'comments', 'enquiry', 'your-comment'),
    );

    /**
     * @return array<string,string[]> Canonical column => list of aliased raw field names.
     */
    public static function aliases()
    {
        return (array) apply_filters('coptrz_cf7_field_aliases', self::DEFAULT_ALIASES);
    }

    /**
     * Resolve the canonical Email/Name/Phone/Message values for one
     * submission's pivoted field array.
     *
     * @param array<string,string> $fields Field name => value, one submission.
     * @return array{email:string,name:string,phone:string,message:string}
     */
    public static function extract(array $fields)
    {
        $normalised = self::normalise_keys($fields);
        $aliases    = self::aliases();

        $email   = self::first_match($normalised, $aliases['email'] ?? array());
        $phone   = self::first_match($normalised, $aliases['phone'] ?? array());
        $message = self::first_match($normalised, $aliases['message'] ?? array());
        $name    = self::first_match($normalised, $aliases['name'] ?? array());

        if ('' === $name) {
            $first = self::first_match($normalised, $aliases['first_name'] ?? array());
            $last  = self::first_match($normalised, $aliases['last_name'] ?? array());
            $name  = trim($first . ' ' . $last);
        }

        return array(
            'email'   => $email,
            'name'    => $name,
            'phone'   => $phone,
            'message' => $message,
        );
    }

    /**
     * @param array<string,string> $fields
     * @return array<string,string> Normalised key => original value.
     */
    private static function normalise_keys(array $fields)
    {
        $out = array();
        foreach ($fields as $key => $value) {
            $out[self::normalise_key((string) $key)] = (string) $value;
        }

        return $out;
    }

    /**
     * @param string $key
     * @return string
     */
    private static function normalise_key($key)
    {
        return strtolower(str_replace('_', '-', trim($key, "_- \t\n\r\0\x0B")));
    }

    /**
     * @param array<string,string> $normalised_fields
     * @param string[]             $candidate_names
     * @return string
     */
    private static function first_match(array $normalised_fields, array $candidate_names)
    {
        foreach ($candidate_names as $candidate) {
            $key = self::normalise_key($candidate);
            if (isset($normalised_fields[$key]) && '' !== $normalised_fields[$key]) {
                return $normalised_fields[$key];
            }
        }

        return '';
    }
}
