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

## Temporary workflow lines to remove before merging into master

While exment `feature/laravel-12` is not merged yet, its workflows have to pin
this repository's `feature/laravel-12` branch. For that purpose the exment
branch `feature/laravel-12-test-ga` adds the following line (plus its TODO
comment) to the `exment-boilerplate` checkout step of
`.github/workflows/unittest.yml`, `.github/workflows/phpstan.yml` and
`.github/workflows/check-lang.yml`:

```yaml
          ref: feature/laravel-12   # <- REMOVE before merging into master
```

Merge order when releasing Laravel 12 support:

1. Merge this repository's `feature/laravel-12` into `main`.
2. In the exment repository, delete every `ref: feature/laravel-12` line (and
   its TODO comment) from the three workflow files above.
3. Merge exment `feature/laravel-12` into `master`.

## Ghi chú (Tiếng Việt)

- Repo này là app Laravel "chủ" để chạy bộ test của `exment` và
  `exment-admin-core`.
- `./exment`: do workflow của repo exment checkout vào (đúng commit PR đang
  test).
- `./exment-admin-core`: nếu thư mục chưa tồn tại, script
  `scripts/ensure-exment-admin-core.php` sẽ tự `git clone` nhánh `master` khi
  chạy `composer install`.
- Trước khi merge exment `feature/laravel-12` vào `master`, PHẢI làm theo thứ
  tự:
  1. Merge nhánh `feature/laravel-12` của repo này vào `main`.
  2. Xoá các dòng `ref: feature/laravel-12` (kèm comment TODO) trong 3 file
     workflow của exment: `unittest.yml`, `phpstan.yml`, `check-lang.yml`.
  3. Merge exment `feature/laravel-12` vào `master`.
