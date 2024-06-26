<?php
$SVG = new SVG;
?>
<div class="search-form d-flex background-white">
    <select name="eael-adv-search-cate-list">
        <option value="">All Categories</option>
        <option value="30">Accessories &amp; Parts</option>
        <option value="1316">Approved Used</option>
        <option value="28">Bundle Packages</option>
        <option value="27">Drones</option>
        <option value="789">E-learning Courses</option>
        <option value="1196">Other</option>
        <option value="29">Payloads &amp; Attachments</option>
        <option value="776">Software</option>
        <option value="32">Training</option>
    </select>
    <input type="text" placeholder="Search for Drones, Software, Guides and more…" class="eael-advanced-search" autocomplete="off">
    <button class="eael-advanced-search-button"><?= $SVG->search() ?></button>
    <div id="search-result">

    </div>
</div>