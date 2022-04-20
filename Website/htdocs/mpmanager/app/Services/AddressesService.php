<?php


namespace App\Services;

use App\Models\Address\Address;
use App\Models\Address\HasAddressesInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AddressesService
{

    public function __construct(private SessionService $sessionService, private Address $address)
    {
        $this->sessionService->setModel($address);
    }

    // fills the object and returns it without saving.
    public function instantiate(array $params): Address
    {
        return $this->address->fill([
            'city_id' => $params['city_id'] ?? null,
            'email' => $params['email'] ?? null,
            'phone' => $params['phone'],
            'street' => $params['street'] ?? null,
            'is_primary' => $params['is_primary'] ?? null
        ]);
    }

    /**
     * @return Model|false
     */
    public function assignAddressToOwner(HasAddressesInterface $owner, Address $address)
    {
        return $owner->addresses()->save($address);
    }


    public function getAddressList(): array
    {
        return $this->address::all();
    }

    public function getAddressById(int $id): Address
    {
        return $this->address::findOrFail($id);
    }

    public function makeAddress(array $addressData): Address
    {
        return $this->address->newQuery()->make([
            'email' => isset($addressData['email']) ?: null,
            'phone' => isset($addressData['phone']) ?: null,
            'street' => isset($addressData['street']) ?: null,
            'city_id' => isset($addressData['city_id']) ?: null,
            'geo_id' => isset($addressData['geo_id']) ?: null,
            'is_primary' => isset($addressData['is_primary']) ?: 0,
        ]);

    }

    public function saveAddress(Address $address): bool
    {
        return $address->save();
    }

    public function updateAddress(Address $address, array $addressData): bool
    {
        return $address->update($addressData);
    }

    public function getStoredAddressWithCityRelation(int $id): Address
    {
        return $this->address::with('city')->findOrFail($id);
    }

    public function createAddressDataFromRequest(Request $request): array
    {
        return [
            'email' => $request->get('email') ?? '',
            'phone' => $request->get('phone') ?? '',
            'street' => $request->get('street') ?? '',
            'city_id' => $request->get('city_id') ?? '',
            'is_primary' => $request->get('is_primary') ?? 1,
        ];
    }
}
