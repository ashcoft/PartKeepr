# CI workflow

`github-actions.yml` is the GitHub Actions CI workflow for this repository.

It could not be committed to `.github/workflows/ci.yml` directly, because the
credential which prepared it does not have the `workflows` permission required
to create or update files in `.github/workflows/`. Enable it with:

```sh
mkdir -p .github/workflows
git mv ci/github-actions.yml .github/workflows/ci.yml
git commit -m "Enable the CI workflow"
```

The file needs no modifications; see `documentation/developer/UNIT-TESTS.md`
for a description of the jobs, the database matrix and the local test setup.
This directory can be removed once the file has been moved.