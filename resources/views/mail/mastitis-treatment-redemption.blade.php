<p>wellkasa Lockout Redemption Form Submission:</p>

<p>---</p>

<p>Form ID: {{ $form_data['promotion_id'] }}</p>
<p>First Name: {{ $form_data['first_name'] }}</p>
<p>Last Name: {{ $form_data['last_name'] }}</p>
<p>Email: {{ $form_data['email'] }}</p>
<p>Phone: {{ $form_data['phone'] }}</p>
<p>Address: {{ $form_data['address'] }}</p>
<p>City: {{ $form_data['city'] }}</p>
<p>Make Cheque Payable to: {{ $form_data['cheque'] }}</p>
<p>Province: {{ strtoupper($form_data['province']) }}</p>
<p>Postal Code: {{ $form_data['postal_code'] }}</p>

<p>---</p>

@if (isset($form_data['veterinarian_name']))<p>Veterinarian: {{ $form_data['veterinarian_name'] }} </p>@endif
@if (isset($form_data['clinic_name']))<p>Clinic: {{ $form_data['clinic_name'] }} </p>@endif

<p>---</p>

@if (isset($form_data['farm_name']))<p>Farm Name: {{ $form_data['farm_name'] }} </p>@endif
@if (isset($form_data['herd_size']))<p>Herd Size: {{ $form_data['herd_size'] }} </p>@endif
@if (isset($form_data['stall_type']))<p>Stall Type: {{ $form_data['stall_type'] }} </p>@endif
@if (isset($form_data['process']))<p>Process: {{ $form_data['process'] }} </p>@endif