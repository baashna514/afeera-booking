@extends('layouts.app', ['title' => 'Edit Company - Owner Portal'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-[#da1705]"></i>
                <span>Edit Company: {{ $company->name }}</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Update company information and status.</p>
        </div>
        <a href="{{ route('owner.companies.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition inline-flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Back to Companies
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('owner.companies.update', $company) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Company Name <span class="text-[#da1705]">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:border-[#da1705] focus:outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Company Code
                </label>
                <input type="text" name="code" value="{{ old('code', $company->code) }}"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:border-[#da1705] focus:outline-none transition uppercase">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Phone Number
                </label>
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:border-[#da1705] focus:outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Address / Head Office
                </label>
                <textarea name="address" rows="3"
                          class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:border-[#da1705] focus:outline-none transition">{{ old('address', $company->address) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Status
                </label>
                <select name="status" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#da1705] focus:border-[#da1705] focus:outline-none transition">
                    <option value="active" {{ old('status', $company->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $company->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('owner.companies.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#da1705] hover:bg-[#b91204] text-white text-xs font-bold rounded-xl shadow-lg shadow-red-900/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Update Company
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
