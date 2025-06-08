<?php
function base_url($path = '') {
    return '/route' . ($path ? '/' . ltrim($path, '/') : '');
}