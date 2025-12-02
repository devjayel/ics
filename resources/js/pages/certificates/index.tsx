import AppLayout from '@/layouts/app-layout';
import { index, create, destroy } from '@/routes/certificates';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Edit, Trash2, Plus, Eye, Download, FileText, Image as ImageIcon } from 'lucide-react';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Certificates',
        href: index().url,
    },
];

interface Rul {
    id: number;
    uuid: string;
    name: string;
    contact_number: string;
}

interface Certificate {
    uuid: string;
    certificate_name: string;
    file_path: string;
    rul: Rul;
    created_at: string;
}

interface PaginatedCertificates {
    data: Certificate[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface IndexProps {
    certificates: PaginatedCertificates;
}

export default function Index({ certificates }: IndexProps) {
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [certificateToDelete, setCertificateToDelete] = useState<string | null>(null);
    const [previewDialogOpen, setPreviewDialogOpen] = useState(false);
    const [previewCertificate, setPreviewCertificate] = useState<Certificate | null>(null);

    const handleDeleteClick = (uuid: string) => {
        setCertificateToDelete(uuid);
        setDeleteDialogOpen(true);
    };

    const confirmDelete = () => {
        if (certificateToDelete) {
            router.delete(destroy({ certificate: certificateToDelete }).url);
            setDeleteDialogOpen(false);
            setCertificateToDelete(null);
        }
    };

    const isImage = (filePath: string) => {
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        const extension = filePath.split('.').pop()?.toLowerCase();
        return extension ? imageExtensions.includes(extension) : false;
    };

    const handlePreview = (certificate: Certificate) => {
        if (isImage(certificate.file_path)) {
            setPreviewCertificate(certificate);
            setPreviewDialogOpen(true);
        } else {
            // Download file
            window.open(`/storage/${certificate.file_path}`, '_blank');
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Certificates" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <div>
                                <CardTitle>Certificates</CardTitle>
                                <CardDescription>
                                    Manage certificates and their associated RULs
                                </CardDescription>
                            </div>
                            <Link href={create().url}>
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Create New
                                </Button>
                            </Link>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Certificate Name</TableHead>
                                        <TableHead>RUL Name</TableHead>
                                        <TableHead>Contact Number</TableHead>
                                        <TableHead>File Type</TableHead>
                                        <TableHead className="text-right">Actions</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {certificates.data.length === 0 ? (
                                        <TableRow>
                                            <TableCell
                                                colSpan={5}
                                                className="h-24 text-center"
                                            >
                                                No certificates found.
                                            </TableCell>
                                        </TableRow>
                                    ) : (
                                        certificates.data.map((certificate) => (
                                            <TableRow key={certificate.uuid}>
                                                <TableCell className="font-medium">
                                                    {certificate.certificate_name}
                                                </TableCell>
                                                <TableCell>{certificate.rul.name}</TableCell>
                                                <TableCell>{certificate.rul.contact_number}</TableCell>
                                                <TableCell>
                                                    <div className="flex items-center gap-2">
                                                        {isImage(certificate.file_path) ? (
                                                            <>
                                                                <ImageIcon className="h-4 w-4 text-blue-600" />
                                                                <span className="text-sm text-blue-600">Image</span>
                                                            </>
                                                        ) : (
                                                            <>
                                                                <FileText className="h-4 w-4 text-gray-600" />
                                                                <span className="text-sm text-gray-600">Document</span>
                                                            </>
                                                        )}
                                                    </div>
                                                </TableCell>
                                                <TableCell className="text-right">
                                                    <div className="flex justify-end gap-2">
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            onClick={() => handlePreview(certificate)}
                                                            title={isImage(certificate.file_path) ? "Preview" : "Download"}
                                                        >
                                                            {isImage(certificate.file_path) ? (
                                                                <Eye className="h-4 w-4" />
                                                            ) : (
                                                                <Download className="h-4 w-4" />
                                                            )}
                                                        </Button>
                                                        <Link
                                                            href={`/certificates/${certificate.uuid}/edit`}
                                                        >
                                                            <Button
                                                                variant="outline"
                                                                size="sm"
                                                            >
                                                                <Edit className="h-4 w-4" />
                                                            </Button>
                                                        </Link>
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                            onClick={() =>
                                                                handleDeleteClick(certificate.uuid)
                                                            }
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </Button>
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        ))
                                    )}
                                </TableBody>
                            </Table>
                        </div>
                        {certificates.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-between">
                                <div className="text-sm text-muted-foreground">
                                    Showing {(certificates.current_page - 1) * certificates.per_page + 1} to{' '}
                                    {Math.min(certificates.current_page * certificates.per_page, certificates.total)} of{' '}
                                    {certificates.total} results
                                </div>
                                <div className="flex gap-2">
                                    {certificates.current_page > 1 && (
                                        <Link
                                            href={index({ query: { page: certificates.current_page - 1 } }).url}
                                        >
                                            <Button variant="outline">Previous</Button>
                                        </Link>
                                    )}
                                    {certificates.current_page < certificates.last_page && (
                                        <Link
                                            href={index({ query: { page: certificates.current_page + 1 } }).url}
                                        >
                                            <Button variant="outline">Next</Button>
                                        </Link>
                                    )}
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Image Preview Dialog */}
            <Dialog open={previewDialogOpen} onOpenChange={setPreviewDialogOpen}>
                <DialogContent className="max-w-4xl">
                    <DialogHeader>
                        <DialogTitle>Certificate Preview</DialogTitle>
                        <DialogDescription>
                            {previewCertificate?.certificate_name}
                        </DialogDescription>
                    </DialogHeader>
                    {previewCertificate && (
                        <div className="mt-4">
                            <img
                                src={`/storage/${previewCertificate.file_path}`}
                                alt={previewCertificate.certificate_name}
                                className="w-full h-auto rounded-lg"
                            />
                        </div>
                    )}
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setPreviewDialogOpen(false)}
                        >
                            Close
                        </Button>
                        <a
                            href={`/storage/${previewCertificate?.file_path}`}
                            download
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <Button>
                                <Download className="mr-2 h-4 w-4" />
                                Download
                            </Button>
                        </a>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Delete Confirmation Dialog */}
            <Dialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Are you absolutely sure?</DialogTitle>
                        <DialogDescription>
                            This action cannot be undone. This will permanently delete the
                            certificate and its associated file.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setDeleteDialogOpen(false)}
                        >
                            Cancel
                        </Button>
                        <Button variant="destructive" onClick={confirmDelete}>
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
