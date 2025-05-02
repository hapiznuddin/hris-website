<div class="p-6 bg-white rounded-lg shadow">
  <h3 class="text-lg font-bold">Karyawan Terbaru</h3>

  <div class="mt-4 overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
          <thead>
              <tr>
                  <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nama</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">NIP</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Jabatan</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Departemen</th>
                  <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
              </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
              @forelse ($data as $employee)
                  <tr>
                      <td class="px-4 py-2 text-sm">{{ $employee->name }}</td>
                      <td class="px-4 py-2 text-sm">{{ $employee->nip }}</td>
                      <td class="px-4 py-2 text-sm">{{ $employee->position }}</td>
                      <td class="px-4 py-2 text-sm">{{ $employee->department }}</td>
                      <td class="px-4 py-2 text-sm">
                          @if($employee->user?->active ?? false)
                              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                  Aktif
                              </span>
                          @else
                              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                  Tidak Aktif
                              </span>
                          @endif
                      </td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="5" class="px-4 py-2 text-sm text-center text-gray-500">Tidak ada data.</td>
                  </tr>
              @endforelse
          </tbody>
      </table>
  </div>
</div>