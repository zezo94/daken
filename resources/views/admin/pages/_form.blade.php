@php
    $editing = isset($page);
    $ar = app()->getLocale() === 'ar';
@endphp

<form
    method="POST"
    action="{{ $editing ? route('admin.pages.update', $page) : route('admin.pages.store') }}"
    enctype="multipart/form-data"
    class="daken-cms__form daken-page-editor"
>
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    @if($errors->any())
        <div class="daken-cms__errors daken-page-editor__errors" role="alert">
            <strong>{{ $ar ? 'يرجى تصحيح الأخطاء التالية:' : 'Please correct the following errors:' }}</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="daken-cms__panel daken-page-editor__panel" aria-labelledby="page-information-heading">
        <div class="daken-page-editor__panel-heading">
            <span class="daken-page-editor__step">01</span>
            <div>
                <h2 id="page-information-heading">{{ $ar ? 'معلومات الصفحة' : 'Page information' }}</h2>
                <p>{{ $ar ? 'حدد رابطًا واضحًا للصفحة. لا يمكن تغيير المفتاح بعد إنشائها.' : 'Choose a clear page URL. The key cannot be changed after creation.' }}</p>
            </div>
        </div>

        <div class="daken-page-editor__fields">
            @if($editing)
                <div class="daken-page-editor__field">
                    <span class="daken-page-editor__label">{{ $ar ? 'مفتاح الرابط' : 'Page URL key' }}</span>
                    <code class="daken-page-editor__readonly" dir="ltr">{{ $page->key }}</code>
                    <p class="daken-page-editor__hint">{{ $ar ? 'المفتاح ثابت للحفاظ على الروابط الحالية.' : 'The key is fixed to preserve existing links.' }}</p>
                </div>
            @else
                <div class="daken-page-editor__field">
                    <label for="page-key">{{ $ar ? 'مفتاح الرابط بالإنجليزية' : 'Page URL key' }} <span aria-hidden="true">*</span></label>
                    <input
                        id="page-key"
                        name="key"
                        type="text"
                        value="{{ old('key') }}"
                        maxlength="100"
                        pattern="[a-z][a-z0-9-]*"
                        placeholder="our-story"
                        dir="ltr"
                        autocomplete="off"
                        required
                        aria-describedby="page-key-hint"
                        @class(['daken-page-editor__invalid' => $errors->has('key')])
                    >
                    <p id="page-key-hint" class="daken-page-editor__hint">{{ $ar ? 'مثال: our-story — رابط الصفحة: /pages/our-story' : 'Example: our-story — Page URL: /pages/our-story' }}</p>
                    @error('key') <p class="daken-page-editor__error">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>
    </section>

    <div class="daken-page-editor__language-grid">
        @foreach(['ar' => 'المحتوى العربي', 'en' => 'English Content'] as $lang => $heading)
            <section
                class="daken-cms__panel daken-page-editor__panel daken-page-editor__language"
                dir="{{ $lang === 'ar' ? 'rtl' : 'ltr' }}"
                aria-labelledby="page-language-{{ $lang }}"
            >
                <div class="daken-page-editor__panel-heading">
                    <span class="daken-page-editor__step">{{ $lang === 'ar' ? '02' : '03' }}</span>
                    <div>
                        <h2 id="page-language-{{ $lang }}">{{ $heading }}</h2>
                        <p>{{ $lang === 'ar' ? 'العنوان والنص الظاهر للزوار باللغة العربية.' : 'Title and content displayed to English visitors.' }}</p>
                    </div>
                </div>

                <div class="daken-page-editor__fields">
                    <div class="daken-page-editor__field">
                        <label for="page-title-{{ $lang }}">{{ $lang === 'ar' ? 'عنوان الصفحة' : 'Page title' }} <span aria-hidden="true">*</span></label>
                        <input
                            id="page-title-{{ $lang }}"
                            name="title[{{ $lang }}]"
                            type="text"
                            maxlength="255"
                            value="{{ old('title.'.$lang, $editing ? ($page->title[$lang] ?? '') : '') }}"
                            required
                            @class(['daken-page-editor__invalid' => $errors->has('title.'.$lang)])
                        >
                        @error('title.'.$lang) <p class="daken-page-editor__error">{{ $message }}</p> @enderror
                    </div>

                    <div class="daken-page-editor__field">
                        <label for="page-body-{{ $lang }}">{{ $lang === 'ar' ? 'محتوى الصفحة' : 'Page content' }} <span aria-hidden="true">*</span></label>
                        <textarea
                            id="page-body-{{ $lang }}"
                            name="body[{{ $lang }}]"
                            rows="12"
                            maxlength="20000"
                            required
                            @class(['daken-page-editor__invalid' => $errors->has('body.'.$lang)])
                        >{{ old('body.'.$lang, $editing ? ($page->body[$lang] ?? '') : '') }}</textarea>
                        @error('body.'.$lang) <p class="daken-page-editor__error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    <section class="daken-cms__panel daken-page-editor__panel" aria-labelledby="page-publishing-heading">
        <div class="daken-page-editor__panel-heading">
            <span class="daken-page-editor__step">04</span>
            <div>
                <h2 id="page-publishing-heading">{{ $ar ? 'الصورة والنشر' : 'Image and publishing' }}</h2>
                <p>{{ $ar ? 'أضف صورة رئيسية وحدد ظهور الصفحة للزوار.' : 'Add a hero image and choose the page visibility.' }}</p>
            </div>
        </div>

        <div class="daken-page-editor__publishing-grid">
            <div class="daken-page-editor__field">
                @if($editing && $page->image_url)
                    <img class="daken-cms__preview daken-page-editor__preview" src="{{ $page->image_url }}" alt="{{ $ar ? 'الصورة الرئيسية الحالية' : 'Current hero image' }}" loading="lazy">
                @endif
                <label for="page-image">{{ $editing ? ($ar ? 'استبدال الصورة (اختياري)' : 'Replace image (optional)') : ($ar ? 'الصورة الرئيسية (اختياري)' : 'Hero image (optional)') }}</label>
                <input id="page-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" aria-describedby="page-image-hint">
                <p id="page-image-hint" class="daken-page-editor__hint">{{ $ar ? 'JPG أو PNG أو WebP، بحد أقصى 2 ميجابايت. لا يوجد خيار لحذف الصور.' : 'JPG, PNG or WebP, up to 2 MB. Image deletion is not available.' }}</p>
                @error('image') <p class="daken-page-editor__error">{{ $message }}</p> @enderror
            </div>

            <div class="daken-page-editor__field">
                <label for="page-status">{{ $ar ? 'حالة النشر' : 'Publishing status' }} <span aria-hidden="true">*</span></label>
                <select id="page-status" name="is_active" required>
                    <option value="1" @selected((string) old('is_active', $editing ? (int) $page->is_active : 0) === '1')>{{ $ar ? 'منشورة' : 'Published' }}</option>
                    <option value="0" @selected((string) old('is_active', $editing ? (int) $page->is_active : 0) === '0')>{{ $ar ? 'مسودة / مخفية' : 'Draft / hidden' }}</option>
                </select>
                <p class="daken-page-editor__hint">{{ $ar ? 'الصفحات المخفية لا تظهر للزوار.' : 'Hidden pages are not visible to visitors.' }}</p>
                @error('is_active') <p class="daken-page-editor__error">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <div class="daken-cms__actions daken-page-editor__actions">
        <button class="daken-cms__button daken-page-editor__submit" type="submit">
            {{ $ar ? 'حفظ الصفحة' : 'Save page' }}
        </button>
        <a class="daken-page-editor__cancel" href="{{ route('admin.pages.index') }}">
            {{ $ar ? 'إلغاء والعودة' : 'Cancel and go back' }}
        </a>
    </div>
</form>
