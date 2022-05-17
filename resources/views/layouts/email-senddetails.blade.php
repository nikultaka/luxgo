<h2>EasyTap</h2>
<h4>Here The Details Of User.</h4>
<p>
<h3><b>Name :</b> {{ $data['name'] }}</h3>
<h3><b>Email :</b> {{ $data['email'] }}</h3>
<h3><b>Phone Number :</b> {{ $data['phonenumber'] }}</h3>
<?php if (isset($data['job']) && $data['job'] && $data['job'] != null) { ?>
    <h3><b>Job Title :</b> {{ $data['job'] }}</h3>
<?php } ?>

<?php if (isset($data['company']) && $data['company'] && $data['company'] != null) { ?>
    <h3><b>Comnpany Name :</b> {{ $data['company'] }}</h3>
<?php } ?>

<?php if (isset($data['note']) && $data['note'] && $data['note'] != null) { ?>
    <h3><b>Note :</b> {{ $data['note'] }}</h3>
<?php } ?>

<p></p>
<p></p>

<h4>Good Luck.</h4>
<h3><a href="https://easytap.co/">Click Here</a> To Buy EasyTap With 10% Off</h3>
<h4>The EasyTap Team</h4>