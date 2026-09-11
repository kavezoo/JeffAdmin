<?php
    /**
     * Flatpickr induló értékek — submit formátum (locale-független):
     *   date:     Y-m-d            pl. 2026-03-15
     *   time:     H:i              pl. 14:30
     *   datetime: Y-m-d H:i:S      pl. 2026-03-15 14:30:00
     * Üres string = üres mező. A megjelenítés (pl. 2026.03.15.) a JS végzi.
     */
    $fpDate = '2026-03-15';
    $fpTime = '14:30';
    $fpDatetime = '2026-03-15 14:30:00';

    $fpDate = $fpDate ?? '';
    $fpTime = $fpTime ?? '';
    $fpDatetime = $fpDatetime ?? '';

    /**
     * Számmező (spinner) — PHP konfig:
     *   $numInteger — true: csak egész; false: tizedes is
     *   $numMin / $numMax — intervallum (null = nincs határ)
     *   $numStep — lépték (egész: 1, tizedes pl. 0.1)
     *   $numValue — induló érték (üres string = üres mező)
     */
    $numInteger = false;
    $numValue = 3.14;

    $numInteger = $numInteger ?? true;
    $numMin = $numMin ?? -100;
    $numMax = $numMax ?? 1000;
    $numStep = $numStep ?? ($numInteger ? 1 : 0.1);
    $numValue = $numValue ?? '';

    /**
     * Multiselect (Tom Select) — példa opciók és kiválasztott értékek.
     * A POST tömb: name="tags[]"
     */
    $tagOptions = [
        'admin' => 'Administration',
        'sales' => 'Sales',
        'marketing' => 'Marketing',
        'support' => 'Support',
        'hardware' => 'Hardware',
        'accessories' => 'Kiegészítők',
        'warranty' => 'Garancia',
        'featured' => 'Kiemelt',
        'new' => 'Új',
        'promo' => 'Akciós',
    ];
    $tagSelected = ['sales', 'hardware', 'featured'];
    $tagOptions = $tagOptions ?? [];
    $tagSelected = $tagSelected ?? [];
?>
                        <div class="row row-tight" style="margin-top: 16px;">
                            <div class="col-12 col-xxl-11">

                                <form action="#" method="post" onsubmit="return false" enctype="multipart/form-data" class="form-horizontal">
                                <div class="card shadow" aria-labelledby="basic-form-title">

                                        <div class="card-header form-card-header">
                                            <div class="form-card-header__title">
                                                <strong id="basic-form-title">Add New</strong>
                                                <small class="d-block">Order item</small>
                                            </div>

                                            <ul class="nav nav-tabs card-header-tabs form-card-header__tabs" id="formTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link active" id="tab-basic-btn" data-bs-toggle="tab" href="#tab-basic" role="tab" aria-controls="tab-basic" aria-current="page" aria-selected="true">Alapadatok</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="tab-details-btn" data-bs-toggle="tab" href="#tab-details" role="tab" aria-controls="tab-details" aria-selected="false">Részletek</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="tab-megjegyzes-btn" data-bs-toggle="tab" href="#tab-megjegyzes" role="tab" aria-controls="tab-megjegyzes" aria-selected="false">Megjegyzés</a>
                                                </li>
                                                <li class="nav-item ms-auto" role="presentation">
                                                    <a class="nav-link" id="tab-settings-btn" data-bs-toggle="tab" href="#tab-settings" role="tab" aria-controls="tab-settings" aria-selected="false">Beállítások</a>
                                                </li>
                                            </ul>

                                            <a href="#" class="m-btn m-btn--ghost form-card-header__close" style="padding: 0 10px;" aria-label="Bezárás" title="Bezárás">
                                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                            </a>
                                        </div>

                                        <div class="card-body form-card-body">
                                            <div class="tab-content">

                                                <div class="tab-pane fade show active" id="tab-basic" role="tabpanel" aria-labelledby="tab-basic-btn" tabindex="0">

                                                    <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="tags" class="form-control-label fw-bold">Címkék:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <div class="select-with-action">
                                                                <select name="tags[]" id="tags" class="form-select" data-tom-select multiple placeholder="Válassz címkéket…">
                                                                    <?php foreach ($tagOptions as $tagValue => $tagLabel) : ?>
                                                                    <option value="<?= htmlspecialchars((string) $tagValue, ENT_QUOTES, 'UTF-8') ?>"<?= in_array((string) $tagValue, $tagSelected, true) ? ' selected' : '' ?>><?= htmlspecialchars((string) $tagLabel, ENT_QUOTES, 'UTF-8') ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <button type="button" class="btn btn-outline-secondary select-with-action__btn" aria-label="További lehetőségek" title="További lehetőségek">
                                                                    <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="tab-pane fade" id="tab-details" role="tabpanel" aria-labelledby="tab-details-btn" tabindex="0">


                                                <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="datetime-input" class="form-control-label fw-bold">Dátumidő:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <input type="text" id="datetime-input" name="datetime_input" class="form-control fp-field fp-field--datetime" placeholder="ÉÉÉÉ.HH.NN. ÓÓ:PP" autocomplete="off" data-fp="datetime" value="<?= htmlspecialchars($fpDatetime, ENT_QUOTES, 'UTF-8') ?>" autofocus>
                                                        </div>
                                                    </div>


                                                    <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="date-input" class="form-control-label fw-bold">Dátum:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <input type="text" id="date-input" name="date_input" class="form-control fp-field fp-field--date" placeholder="ÉÉÉÉ.HH.NN." autocomplete="off" data-fp="date" value="<?= htmlspecialchars($fpDate, ENT_QUOTES, 'UTF-8') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="time-input" class="form-control-label fw-bold">Idő:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <input type="text" id="time-input" name="time_input" class="form-control fp-field fp-field--time" placeholder="ÓÓ:PP" autocomplete="off" data-fp="time" value="<?= htmlspecialchars($fpTime, ENT_QUOTES, 'UTF-8') ?>">
                                                        </div>
                                                    </div>
                                                                                                        
                                                    <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="integer-input" class="form-control-label fw-bold"><?= $numInteger ? 'Egész szám:' : 'Szám:' ?></label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <input type="text" id="integer-input" name="integer_input" placeholder="<?= $numInteger ? '0' : '0,0' ?>" class="form-control" data-number-spinner
                                                                data-integer="<?= $numInteger ? '1' : '0' ?>" autocomplete="off" inputmode="<?= $numInteger ? 'numeric' : 'decimal' ?>"  step="<?= htmlspecialchars((string) $numStep, ENT_QUOTES, 'UTF-8') ?>"
                                                                <?php if ($numMin !== null && $numMin !== '') : ?>min="<?= htmlspecialchars((string) $numMin, ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                                                                <?php if ($numMax !== null && $numMax !== '') : ?>max="<?= htmlspecialchars((string) $numMax, ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                                                                <?php if ($numValue !== '' && $numValue !== null) : ?>value="<?= htmlspecialchars((string) $numValue, ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                                                            >
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="file-input" class="form-control-label fw-bold">File Input:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <div class="file-picker">
                                                                <input type="file" id="file-input" name="file-input" class="file-picker__input">
                                                                <label for="file-input" class="file-picker__control">
                                                                    <span class="file-picker__btn">
                                                                        <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
                                                                        Browse
                                                                    </span>
                                                                    <span class="file-picker__name" data-file-name>No file chosen</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="select" class="form-control-label fw-bold">Select:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <div class="select-with-action">
                                                                <select name="select" id="select" class="form-select" data-tom-select>
                                                                    <option value="">Please select</option>
                                                                    <option value="1">Option #1</option>
                                                                    <option value="2">Option #2</option>
                                                                    <option value="3">Option #3</option>
                                                                    <option value="4">Option #4</option>
                                                                    <option value="5">Option #5</option>
                                                                    <option value="6">Option #6</option>
                                                                    <option value="7">Option #7</option>
                                                                    <option value="8">Option #8</option>
                                                                    <option value="9">Option #9</option>
                                                                    <option value="10">Option #10</option>
                                                                    <option value="11">Option #11</option>
                                                                    <option value="12">Option #12</option>
                                                                    <option value="13">Option #13</option>
                                                                    <option value="14">Option #14</option>
                                                                    <option value="15">Option #15</option>
                                                                </select>
                                                                <button type="button" class="btn btn-outline-secondary select-with-action__btn" aria-label="További lehetőségek" title="További lehetőségek">
                                                                    <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="text-input" class="form-control-label fw-bold">Text Input:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <input type="text" id="text-input" name="text-input" placeholder="Text" class="is-invalid form-control" autofocus>
                                                        </div>
                                                        <div class="col-12 offset-md-2">
                                                            <small class="form-text fw-bold text-danger">This is an error text</small>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3 align-items-start">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="textarea-input" class="form-control-label fw-bold">Textarea:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <textarea name="textarea-input" id="textarea-input" rows="9" placeholder="Content..." class="form-control"></textarea>
                                                        </div>
                                                    </div>


                                                    <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="selectLg" class="form-control-label fw-bold">Select Large:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <div class="select-with-action">
                                                                <select name="selectLg" id="selectLg" class="form-select" data-tom-select>
                                                                    <option value="">Please select</option>
                                                                    <option value="1">Option #1</option>
                                                                    <option value="2">Option #2</option>
                                                                    <option value="3">Option #3</option>
                                                                    <option value="4">Option #4</option>
                                                                    <option value="5">Option #5</option>
                                                                    <option value="6">Option #6</option>
                                                                    <option value="7">Option #7</option>
                                                                    <option value="8">Option #8</option>
                                                                    <option value="9">Option #9</option>
                                                                    <option value="10">Option #10</option>
                                                                    <option value="11">Option #11</option>
                                                                    <option value="12">Option #12</option>
                                                                    <option value="13">Option #13</option>
                                                                    <option value="14">Option #14</option>
                                                                    <option value="15">Option #15</option>
                                                                </select>
                                                                <button type="button" class="btn btn-outline-secondary select-with-action__btn" aria-label="További lehetőségek" title="További lehetőségek">
                                                                    <i class="fa-solid fa-ellipsis" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3 align-items-center">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label class="form-control-label fw-bold mb-0">Inline Checkboxes:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9 d-flex align-items-center flex-wrap pt-1">
                                                            <div class="form-check form-check-inline">
                                                                <input type="checkbox" id="inline-checkbox1" name="inline-checkbox1" value="option1" class="form-check-input">
                                                                <label for="inline-checkbox1" class="form-check-label">One</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="checkbox" id="inline-checkbox2" name="inline-checkbox2" value="option2" class="form-check-input">
                                                                <label for="inline-checkbox2" class="form-check-label">Two</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input type="checkbox" id="inline-checkbox3" name="inline-checkbox3" value="option3" class="form-check-input">
                                                                <label for="inline-checkbox3" class="form-check-label">Three</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="tab-megjegyzes" role="tabpanel" aria-labelledby="tab-megjegyzes-btn" tabindex="0">
                                                    <div class="form-wysiwyg">
                                                        <textarea id="megjegyzes" name="megjegyzes" class="form-wysiwyg__editor" aria-label="Megjegyzés"></textarea>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="tab-settings" role="tabpanel" aria-labelledby="tab-settings-btn" tabindex="0">
                                                    <section class="form-section">
                                                        <h5 class="form-section__title">A rekord beállításai</h5>

                                                    <div class="row mb-3 align-items-center">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="visible" class="form-control-label fw-bold mb-0">Visible:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9 d-flex align-items-center flex-wrap pt-1">
                                                            <div class="form-check form-check-inline mb-0">
                                                                <input type="checkbox" id="visible" name="visible" value="1" class="form-check-input" checked>
                                                                <label for="visible" class="form-check-label">Látható</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-12 col-md-2 text-start text-md-end">
                                                            <label for="pos" class="form-control-label fw-bold">Pos:</label>
                                                        </div>
                                                        <div class="col-12 col-md-9">
                                                            <input type="text" id="pos" name="pos" value="1000" class="form-control" data-number-spinner data-integer="1" autocomplete="off" inputmode="numeric" min="0" step="10" placeholder="0">
                                                        </div>
                                                    </div>
                                                    </section>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="card-footer border-top">
                                            <div class="offset-md-2">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Save
                                                </button>
                                                <button type="reset" class="btn btn-secondary">
                                                    <i class="fa-solid fa-xmark" aria-hidden="true"></i> Cancel
                                                </button>
                                            </div>
                                        </div>

                                </div>
                                </form>
                            </div>
                        </div>
