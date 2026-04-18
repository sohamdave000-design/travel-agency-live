$extensions = @("*.php", "*.html", "*.js", "*.css")
Get-ChildItem -Path . -Include $extensions -Recurse | ForEach-Object {
    # Check if file is not convert.ps1
    if ($_.Name -ne "convert.ps1") {
        $content = Get-Content $_.FullName -Raw
        if ($null -ne $content) {
            $newContent = $content -replace '\.php', '.html'
            if ($content -cne $newContent) {
                Set-Content -Path $_.FullName -Value $newContent -Encoding UTF8
            }
        }
    }
}

Get-ChildItem -Path . -Filter *.php -Recurse | Where-Object { $_.Name -ne "convert.ps1" } | Rename-Item -NewName { [System.IO.Path]::ChangeExtension($_.Name, ".html") }
