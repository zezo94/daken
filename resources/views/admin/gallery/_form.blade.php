@php
    $editing = isset($image);
    $ar = app()->getLocale() === 'ar';
    $categories = ['products' => 'المنتجات', 'packaging' => 'التغليف', 'campaigns' => 'الحملات', 'events' => 'الفعاليات', 'general' => 'عام'];
@endphp
<form class="dg-admin-form" method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.gallery.update', $image) : route('admin.gallery.store') }}">
    @csrf
    @if($editing) @method('PUT') @endif
    @if($errors->any())
        <div class="dg-alert" role="alert"><strong>يرجى تصحيح الأخطاء:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="dg-admin-panel">
        <h2>المحتوى العربي والإنجليزي</h2>
        <div class="dg-admin-columns">
            @foreach(['ar' => 'العربية', 'en' => 'English'] as $lang => $label)
                <section dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}">
                    <h3>{{ $label }}</h3>
                    <label for="title-{{ $lang }}">{{ $lang === 'ar' ? 'عنوان الصورة' : 'Image title' }}</label>
                    <input id="title-{{ $lang }}" name="title[{{ $lang }}]" required maxlength="255" value="{{ old('title.'.$lang, $editing ? ($image->title[$lang] ?? '') : '') }}">
                    @error('title.'.$lang)<small class="dg-error">{{ $message }}</small>@enderror
                    <label for="desc-{{ $lang }}">{{ $lang === 'ar' ? 'الوصف (اختياري)' : 'Description (optional)' }}</label>
                    <textarea id="desc-{{ $lang }}" name="description[{{ $lang }}]" rows="5" maxlength="3000">{{ old('description.'.$lang, $editing ? ($image->description[$lang] ?? '') : '') }}</textarea>
                    @error('description.'.$lang)<small class="dg-error">{{ $message }}</small>@enderror
                </section>
            @endforeach
        </div>
    </div>
    <div class="dg-admin-panel">
        <h2>الصورة وإعدادات النشر</h2>
        @if($editing)<img class="dg-preview" src="{{ $image->image_url }}" alt="{{ $image->title['ar'] ?? '' }}">@endif
        <label for="gallery-file">{{ $editing ? 'استبدال الصورة (اختياري)' : 'اختر صورة' }}</label>
        <input id="gallery-file" name="image" type="file" accept="image/jpeg,image/png,image/webp" @required(!$editing)>
        <small>JPG / PNG / WebP — الحد الأقصى 5 MB. لا يوجد حذف للصور.</small>
        @error('image')<small class="dg-error">{{ $message }}</small>@enderror
        <div class="dg-admin-columns">
            <div><label for="gallery-category">التصنيف</label><select id="gallery-category" name="category" required>@foreach($categories as $value => $label)<option value="{{ $value }}" @selected(old('category', $editing ? $image->category : 'general') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div><label for="gallery-order">ترتيب الظهور</label><input id="gallery-order" type="number" name="sort_order" min="0" max="999999" required value="{{ old('sort_order', $editing ? $image->sort_order : 0) }}"></div>
        </div>
        <label for="gallery-status">حالة النشر</label>
        <select id="gallery-status" name="is_active" required>
            <option value="1" @selected((string)old('is_active', $editing ? (int)$image->is_active : 0) === '1')>منشورة</option>
            <option value="0" @selected((string)old('is_active', $editing ? (int)$image->is_active : 0) === '0')>مسودة / مخفية</option>
        </select>
    </div>
    <div class="dg-actions"><button type="submit" class="dg-button">حفظ الصورة</button><a href="{{ route('admin.gallery.index') }}">إلغاء والعودة</a></div>
</form>
