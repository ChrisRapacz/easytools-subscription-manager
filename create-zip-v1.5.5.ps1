Add-Type -AssemblyName System.IO.Compression.FileSystem

$sourceDir = 'easytools-subscription-manager'
$zipPath = 'easytools-subscription-manager-v1.5.5.zip'

# Remove existing zip if present
if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

# Create zip file
$zip = [System.IO.Compression.ZipFile]::Open($zipPath, 'Create')

# Add all files with proper Unix-style paths
Get-ChildItem -Path $sourceDir -Recurse -File | ForEach-Object {
    $relativePath = $_.FullName.Substring((Resolve-Path $sourceDir).Path.Length + 1).Replace('\', '/')
    $entryName = 'easytools-subscription-manager/' + $relativePath
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $_.FullName, $entryName, 'Optimal') | Out-Null
    Write-Host "Added: $entryName"
}

$zip.Dispose()
Write-Host "`nZip file created successfully: $zipPath"
