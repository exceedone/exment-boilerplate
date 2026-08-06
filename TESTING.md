# Testing Exment with this boilerplate

This repository is the **host Laravel application** used to run the test suites
of [exceedone/exment](https://github.com/exceedone/exment) and
[exceedone/exment-admin-core](https://github.com/exceedone/exment-admin-core).

CI (GitHub Actions on the `exment` repository) and local testing both run
through [exceedone/docker-exment](https://github.com/exceedone/docker-exment):

```
docker-exment/
└── exment-boilerplate/          <- this repository
    ├── exment/                  <- exment revision under test (git checkout)
    └── exment-admin-core/       <- auto-cloned by scripts/ensure-exment-admin-core.php
```

`composer.dev.json` / `composer.dev.lock` are copied to `composer.json` /
`composer.lock` inside the PHP container, and both `exceedone/exment` and
`exceedone/exment-admin-core` are installed from **path repositories**
(`./exment`, `./exment-admin-core`), so whatever revision is present in those
directories is what gets tested.

- `./exment` must be provided by the caller. The workflows of the exment
  repository check out the pull-request revision there.
- `./exment-admin-core` is cloned automatically from the `master` branch of
  exceedone/exment-admin-core by `scripts/ensure-exment-admin-core.php`
  (composer `pre-install-cmd`) when the directory does not exist. To test a
  specific admin-core revision, place that revision in `./exment-admin-core`
  before running `composer install`.

## Which boilerplate branch to use

| exment branch under test | boilerplate branch |
|--------------------------|--------------------|
| `master` (Laravel 10)    | `main`             |
| `feature/laravel-12`     | `feature/laravel-12` |

## The exment `feature/laravel-12-test-ga` branch

CI for Laravel 12 was verified through the throwaway exment branch
`feature/laravel-12-test-ga` (= `feature/laravel-12` + the changes below). It
carries two kinds of change, and only one of them is temporary.

### Temporary — delete before merging into master

The `exment-boilerplate` checkout step of `.github/workflows/unittest.yml`,
`.github/workflows/phpstan.yml` and `.github/workflows/check-lang.yml` pins this
repository's branch:

```yaml
          # ===== TEMPORARY: remove from here down to "END TEMPORARY" before merging into master =====
          # ...
          ref: feature/laravel-12
          # ===== END TEMPORARY =====
```

Without it the workflows check out this repository's default branch `main`,
which still ships the Laravel 10 files, and `artisan package:discover` fails
with `Class "ExmentAdminCore\Admin\AdminServiceProvider" not found`.

Also temporary: `.github/LARAVEL12-TEST-NOTES.md` in the exment repository.

### Not temporary — port to `feature/laravel-12`

Commit `fix(phpstan): point at larastan v3 and ship the baseline` is a real fix
and must be cherry-picked onto exment `feature/laravel-12`:

- `phpstan.neon.dist` includes `./vendor/larastan/larastan/extension.neon`
  instead of the abandoned `./vendor/nunomaduro/larastan/extension.neon`.
- the `setup phpstan` step of `.github/workflows/phpstan.yml` also copies
  `phpstan-baseline.neon` to the application root, because PHPStan resolves
  relative paths against the directory of the config file.

### Merge order when releasing Laravel 12 support

1. Merge this repository's `feature/laravel-12` into `main`.
2. Cherry-pick the phpstan commit onto exment `feature/laravel-12`; do **not**
   carry over the `TEMPORARY` blocks or `LARAVEL12-TEST-NOTES.md`.
3. Merge exment `feature/laravel-12` into `master`.

## Ghi chú (Tiếng Việt)

- Repo này là app Laravel "chủ" để chạy bộ test của `exment` và
  `exment-admin-core`.
- `./exment`: do workflow của repo exment checkout vào (đúng commit PR đang
  test).
- `./exment-admin-core`: nếu thư mục chưa tồn tại, script
  `scripts/ensure-exment-admin-core.php` sẽ tự `git clone` nhánh `master` khi
  chạy `composer install`.
- Nhánh `feature/laravel-12-test-ga` của repo exment chỉ để test CI, **không
  merge vào `master`**. Nhánh đó chứa 2 loại thay đổi:

  **Tạm thời — phải xoá trước khi merge vào `master`:**
  1. Khối `ref: feature/laravel-12` (nằm giữa 2 dòng `TEMPORARY` và
     `END TEMPORARY`) trong 3 file workflow `unittest.yml`, `phpstan.yml`,
     `check-lang.yml`.
  2. File `.github/LARAVEL12-TEST-NOTES.md`.

  **Không tạm thời — phải cherry-pick sang `feature/laravel-12`:** commit
  `fix(phpstan): point at larastan v3 and ship the baseline`, gồm sửa
  `phpstan.neon.dist` trỏ sang `./vendor/larastan/larastan/extension.neon` và
  bước `setup phpstan` copy thêm `phpstan-baseline.neon`.

- Thứ tự khi phát hành Laravel 12:
  1. Merge nhánh `feature/laravel-12` của repo này vào `main`.
  2. Cherry-pick commit phpstan ở trên sang exment `feature/laravel-12`.
  3. Merge exment `feature/laravel-12` vào `master`.
