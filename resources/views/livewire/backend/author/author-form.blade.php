 <form enctype="multipart/form-data">
     @csrf

     <div class="mb-3">
         <label for="profile_photo">Author Picture</label>
         {{-- Check if image uploaded show the image 100px otherwise nothing --}}
         @if ($author_photo)
             <div class="mb-2">
                 <img src="{{ $author_photo->temporaryUrl() }}" alt="Profile Photo" width="100px" />
             </div>
         @endif
         <input type="file" accept=".jpg, .jpeg, .png" wire:model="author_photo" class="form-control form-control-lg"
             id="author_photo" />
     </div>

     {{-- Name and lastname row --}}
     <div class="row mb-3">
         <div class="col ">
             <label for="cat_name">Author name</label>
             <input name="cat_name" id="cat_name" @class([
                 'form-control form-control-lg',
                 'border-danger' => $errors->has('name'),
             ]) type="text"
                 placeholder="Enter author name" wire:model="name" />
             @error('name')
                 <span class="text-danger">{{ $message }}</span>
             @enderror
         </div>

         <div class="col">
             <label for="lastname">Last name</label>
             <input name="lastname" id="lastname" @class([
                 'form-control form-control-lg',
                 'border-danger' => $errors->has('lastname'),
             ]) type="text"
                 placeholder="Enter author name" wire:model="lastname" />
             @error('lastname')
                 <span class="text-danger">{{ $message }}</span>
             @enderror
         </div>
     </div>

     <div class="mb-3">
         <label for="website">Website</label>
         <input name="website" id="website" @class([
             'form-control form-control-lg',
             'border-danger' => $errors->has('website'),
         ]) type="text"
             placeholder="Enter website URL" wire:model="website" />
         @error('website')
             <span class="text-danger">{{ $message }}</span>
         @enderror
     </div>

     <div class="mb-3">
         <label for="country">Country</label>
         <input name="country" id="country" @class([
             'form-control form-control-lg',
             'border-danger' => $errors->has('country'),
         ]) type="text" placeholder="Enter country"
             wire:model="country" />
         @error('country')
             <span class="text-danger">{{ $message }}</span>
         @enderror
     </div>

     <div class="mb-3">
         <label for="birthdate">Birth date</label>
         <input name="birthdate" id="birthdate" @class([
             'form-control form-control-lg',
             'border-danger' => $errors->has('birthdate'),
         ]) type="date"
             placeholder="Enter birth date" wire:model="birthdate" />
         @error('birthdate')
             <span class="text-danger">{{ $message }}</span>
         @enderror
     </div>

     <div class="mb-3">
         <label for="deathdate">Death date</label>
         <input name="deathdate" id="deathdate" @class([
             'form-control form-control-lg',
             'border-danger' => $errors->has('deathdate'),
         ]) type="date"
             placeholder="Enter death date" wire:model="deathdate" />
         @error('deathdate')
             <span class="text-danger">{{ $message }}</span>
         @enderror
     </div>



     <div class="mb-3">
         <label for="cat_desc">Bio</label>
         <textarea class="form-control form-control-lg mx-3" name="bio" id="bio" type="text"
             placeholder="Enter author bio" wire:model="bio" style="height: 350px;"></textarea>
     </div>
     <div class="d-flex justify-content-end">
         <input class="btn btn-md btn-success rounded-3" type="submit" value="Add" />
     </div>
 </form>
