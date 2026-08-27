<?php

namespace App\Actions\Fortify;

use App\Models\Extension;
use App\Models\MLM;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'firstname' => ['required', 'string', 'max:60'],
            'lastname' => ['required', 'string', 'max:60'],
            'username' => ['required','string', 'alpha_num', 'unique:users', 'min:6'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        $referBy = session()->get('reference');
        $referUser = $referBy
            ? User::where('username', $referBy)->first()
            : null;

        return DB::transaction(function () use ($input, $referBy, $referUser) {
            $user = User::create([
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'name' => $input['firstname'].' '.$input['lastname'],
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'username' => $input['username'],
                'ref_by' => $referUser ? $referUser->id : null,
                'status' => '1',
                'role_id' => '2',
            ]);

            // Extension 3 is optional. A missing extension record must not make
            // an otherwise valid registration fail with a server error.
            if (optional(Extension::find(3))->status == 1) {
                MLM::firstOrCreate(['username' => $input['username']]);

                if ($referUser) {
                    $ref = MLM::where('username', $referBy)->lockForUpdate()->first();

                    if ($ref && $ref->left === null) {
                        $ref->left = $input['username'];
                        $ref->save();
                    } elseif ($ref && $ref->right === null) {
                        $ref->right = $input['username'];
                        $ref->save();
                    }
                }
            }

            return $user;
        });
    }
}
