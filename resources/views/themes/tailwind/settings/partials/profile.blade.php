@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form action="{{ route('wave.settings.profile.put') }}" method="POST" enctype="multipart/form-data">
	<div class="relative flex flex-col px-10 py-8 lg:flex-row"> 
		<div class="w-full lg:w-9/12 xl:w-4/5">
			<div>
				<label for="name" class="block text-sm font-medium leading-5 text-gray-700">Name</label>
				<div class="mt-1 rounded-md shadow-sm">
					<input id="name" type="text" name="name" placeholder="Name" value="{{ Auth::user()->name }}" required class="w-full form-input">
				</div>
			</div>

			<div class="mt-5">
				<label for="email" class="block text-sm font-medium leading-5 text-gray-700">Email Address</label>
				<div class="mt-1 rounded-md shadow-sm">
					<input id="email" type="text" name="email" placeholder="Email Address" value="{{ Auth::user()->email }}" required class="w-full form-input">
				</div>
			</div>
            
            <div class="mt-5">
                <label for="email" class="block text-sm font-medium leading-5 text-gray-700">Company Name</label>
                <div class="mt-1 rounded-md shadow-sm">
                    <input id="company_name" type="text" name="company_name" placeholder="Company Name" value="{{ Auth::user()->company_name }}" required class="w-full form-input">
                </div>
            </div>
            
            <!--
			<div class="mt-5">
				<label for="about" class="block text-sm font-medium leading-5 text-gray-700">About</label>
				<div class="mt-1 rounded-md">
					{!! profile_field('text_area', 'about') !!}
				</div>
			</div>
            -->

			<div class="flex justify-end w-full">
				<button class="flex self-end justify-center w-auto px-4 py-2 mt-5 text-sm font-medium text-white transition duration-150 ease-in-out border border-transparent rounded-md bg-wave-600 hover:bg-wave-500 focus:outline-none focus:border-wave-700 focus:shadow-outline-wave active:bg-wave-700" dusk="update-profile-button">Save</button>
			</div>
		</div>
	</div>

	{{ csrf_field() }}



</form>
